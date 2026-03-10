<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use App\Models\Scm\MaterialRequest;
use App\Models\Scm\ProcurementPayment;
use App\Models\Scm\PurchaseInvoice;
use App\Models\Scm\RequestForQuotation;
use App\Models\Scm\RfqResponse;
use App\Models\Scm\ScmPurchaseOrder;
use App\Models\Scm\ScmPurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProcurementController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // MAIN PAGE RENDER
    // ──────────────────────────────────────────────────────────────

    public function procurement()
    {
        $materialRequests = MaterialRequest::with('material')
            ->orderByRaw("FIELD(urgency, 'High', 'Medium', 'Low')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'req_number' => $r->req_number,
                'material_id' => $r->material_id,
                'material_name' => $r->material?->name ?? $r->material_name,
                'category' => $r->material?->category ?? $r->category,
                'unit' => $r->material?->unit ?? $r->unit,
                'current_stock' => $r->current_stock,
                'reorder_point' => $r->reorder_point,
                'required_qty' => $r->required_qty,
                'urgency' => $r->urgency,
                'requested_by' => $r->requested_by,
                'requested_at' => $r->requested_at?->format('Y-m-d'),
                'status' => $r->status,
                'notes' => $r->notes,
            ]);

        $suppliers = Supplier::orderBy('business_name')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'business_name' => $s->business_name,
                'representative_name' => $s->representative_name,
                'email' => $s->email,
                'phone_number' => $s->phone_number,
                'address' => $s->address,
                'rating' => $s->rating ?? 4.5,
                'status' => $s->status ?? 'Approved',
                'categories' => $s->categories ?? [],
            ]);

        $rfqs = RequestForQuotation::with('responses.supplier')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($rfq) => [
                'id' => $rfq->id,
                'rfq_number' => $rfq->rfq_number,
                'mr_ref' => $rfq->mr_ref,
                'material_name' => $rfq->material_name,
                'category' => $rfq->category,
                'unit' => $rfq->unit,
                'required_qty' => $rfq->required_qty,
                'deadline' => $rfq->deadline?->format('Y-m-d'),
                'sent_at' => $rfq->sent_at?->format('Y-m-d'),
                'status' => $rfq->status,
                'notes' => $rfq->notes,
                'responses' => $rfq->responses->map(fn ($r) => [
                    'id' => $r->id,
                    'supplier_id' => $r->supplier_id,
                    'supplier_name' => $r->supplier?->business_name ?? $r->supplier_name,
                    'unit_price' => $r->unit_price,
                    'total_price' => $r->total_price,
                    'lead_time' => $r->lead_time,
                    'validity_date' => $r->validity_date?->format('Y-m-d'),
                    'payment_terms' => $r->payment_terms,
                    'notes' => $r->notes,
                    'status' => $r->status,
                    'submitted_at' => $r->submitted_at?->format('Y-m-d'),
                ]),
            ]);

        $purchaseOrders = ScmPurchaseOrder::with('items')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($po) => [
                'id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_id' => $po->supplier_id,
                'supplier_name' => $po->supplier_name,
                'rfq_ref' => $po->rfq_ref,
                'issued_date' => $po->issued_date?->format('Y-m-d'),
                'expected_delivery' => $po->expected_delivery?->format('Y-m-d') ?? $po->expected_delivery,
                'status' => $po->status,
                'subtotal' => $po->subtotal,
                'tax_rate' => $po->tax_rate,
                'tax_amount' => $po->tax_amount,
                'grand_total' => $po->grand_total,
                'notes' => $po->notes,
                'received' => $po->received ?? false,
                'items' => $po->items->map(fn ($i) => [
                    'id' => $i->id,
                    'material_name' => $i->material_name,
                    'qty' => $i->qty,
                    'unit' => $i->unit,
                    'unit_price' => $i->unit_price,
                    'total' => $i->total,
                ]),
            ]);

        $invoices = PurchaseInvoice::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($inv) => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'po_number' => $inv->po_number,
                'supplier_name' => $inv->supplier_name,
                'invoice_date' => $inv->invoice_date?->format('Y-m-d'),
                'due_date' => $inv->due_date?->format('Y-m-d'),
                'amount' => $inv->amount,
                'payment_terms' => $inv->payment_terms,
                'status' => $inv->status,
                'received_at' => $inv->received_at?->format('Y-m-d'),
            ]);

        $payments = ProcurementPayment::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'payment_number' => $p->payment_number,
                'invoice_number' => $p->invoice_number,
                'supplier_name' => $p->supplier_name,
                'paid_date' => $p->paid_date?->format('Y-m-d'),
                'amount' => $p->amount,
                'method' => $p->method,
                'bank_reference' => $p->bank_reference,
                'remarks' => $p->remarks,
                'status' => $p->status,
            ]);

        $stats = [
            'pendingMaterialRequests' => $materialRequests->where('status', 'pending')->count(),
            'activeRFQs' => $rfqs->whereIn('status', ['sent', 'partial_response'])->count(),
            'pendingPOs' => $purchaseOrders->whereIn('status', ['draft', 'sent'])->count(),
            'unpaidInvoices' => $invoices->where('status', 'unpaid')->count(),
        ];

        return Inertia::render('Dashboard/SCM/Manager/procurement', compact(
            'materialRequests', 'suppliers', 'rfqs',
            'purchaseOrders', 'invoices', 'payments', 'stats'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // CREATE & SEND RFQ
    // ──────────────────────────────────────────────────────────────

    public function createRFQ(Request $request)
    {
        $validated = $request->validate([
            'mr_id' => 'required|exists:material_requests,id',
            'material_name' => 'required|string',
            'required_qty' => 'required|numeric|min:1',
            'unit' => 'required|string',
            'deadline' => 'required|date|after:today',
            'delivery_address' => 'nullable|string',
            'payment_terms' => 'required|string',
            'notes' => 'nullable|string',
            'selected_suppliers' => 'required|array|min:1',
            'selected_suppliers.*' => 'exists:suppliers,id',
        ]);

        DB::beginTransaction();
        try {
            $mr = MaterialRequest::findOrFail($validated['mr_id']);

            $rfq = RequestForQuotation::create([
                'rfq_number' => $this->generateRFQNumber(),
                'mr_ref' => $mr->req_number,
                'mr_id' => $mr->id,
                'material_id' => $mr->material_id,
                'material_name' => $validated['material_name'],
                'category' => $mr->category,
                'unit' => $validated['unit'],
                'required_qty' => $validated['required_qty'],
                'deadline' => $validated['deadline'],
                'delivery_address' => $validated['delivery_address'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'],
                'sent_at' => now(),
                'status' => 'sent',
                'supplier_ids' => json_encode($validated['selected_suppliers']),
            ]);

            // Update material request status
            $mr->update(['status' => 'rfq_sent']);

            // TODO: Fire RFQSentToSupplier notification/email event for each supplier
            // foreach ($validated['selected_suppliers'] as $supplierId) {
            //     event(new RFQSentToSupplier($rfq, $supplierId));
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'rfq' => array_merge($rfq->toArray(), [
                    'sent_at' => $rfq->sent_at->format('Y-m-d'),
                    'deadline' => $rfq->deadline->format('Y-m-d'),
                    'responses' => [],
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RFQ creation failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to create RFQ.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // ACCEPT QUOTATION → CREATE PO
    // ──────────────────────────────────────────────────────────────

    public function acceptQuotation(Request $request, $responseId)
    {
        $validated = $request->validate([
            'rfq_id' => 'required|exists:request_for_quotations,id',
        ]);

        DB::beginTransaction();
        try {
            $response = RfqResponse::with('rfq')->findOrFail($responseId);
            $rfq = $response->rfq;
            $supplier = Supplier::findOrFail($response->supplier_id);

            // Mark this response as accepted
            $response->update(['status' => 'accepted']);

            // Decline all other pending responses for same RFQ
            RfqResponse::where('rfq_id', $rfq->id)
                ->where('id', '!=', $responseId)
                ->where('status', 'pending_review')
                ->update(['status' => 'declined']);

            // Calculate amounts
            $subtotal = $response->total_price;
            $taxRate = 10;
            $taxAmount = $subtotal * ($taxRate / 100);
            $grandTotal = $subtotal + $taxAmount;

            // Create SCM Purchase Order
            $po = ScmPurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->business_name,
                'rfq_ref' => $rfq->rfq_number,
                'rfq_id' => $rfq->id,
                'issued_date' => now(),
                'expected_delivery' => $response->lead_time,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'notes' => '',
                'received' => false,
            ]);

            // Create PO item
            ScmPurchaseOrderItem::create([
                'scm_purchase_order_id' => $po->id,
                'material_id' => $rfq->material_id,
                'material_name' => $rfq->material_name,
                'qty' => $rfq->required_qty,
                'unit' => $rfq->unit,
                'unit_price' => $response->unit_price,
                'total' => $response->total_price,
            ]);

            // Update RFQ status
            $rfq->update(['status' => 'responded']);

            DB::commit();

            return response()->json([
                'success' => true,
                'purchase_order' => array_merge($po->toArray(), [
                    'issued_date' => $po->issued_date->format('Y-m-d'),
                    'expected_delivery' => $response->lead_time,
                    'items' => [[
                        'id' => 1,
                        'material_name' => $rfq->material_name,
                        'qty' => $rfq->required_qty,
                        'unit' => $rfq->unit,
                        'unit_price' => $response->unit_price,
                        'total' => $response->total_price,
                    ]],
                    'received' => false,
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Accept quotation failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to accept quotation.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // DECLINE QUOTATION
    // ──────────────────────────────────────────────────────────────

    public function declineQuotation(Request $request, $responseId)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $response = RfqResponse::findOrFail($responseId);
            $response->update([
                'status' => 'declined',
                'decline_reason' => $validated['reason'],
            ]);

            // TODO: Notify supplier of decline
            // event(new QuotationDeclined($response));

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Decline quotation failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to decline quotation.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // SEND PURCHASE ORDER TO SUPPLIER
    // ──────────────────────────────────────────────────────────────

    public function sendPurchaseOrder($poId)
    {
        try {
            $po = ScmPurchaseOrder::findOrFail($poId);
            $po->update(['status' => 'sent']);

            // TODO: Send PO email to supplier
            // event(new PurchaseOrderSentToSupplier($po));

            return response()->json(['success' => true, 'status' => 'sent']);
        } catch (\Exception $e) {
            Log::error('Send PO failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to send PO.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // PROCESS PAYMENT
    // ──────────────────────────────────────────────────────────────

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:purchase_invoices,id',
            'invoice_number' => 'required|string',
            'supplier_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'bank_reference' => 'required|string',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice = PurchaseInvoice::findOrFail($validated['invoice_id']);

            $payment = ProcurementPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'supplier_name' => $invoice->supplier_name,
                'paid_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'bank_reference' => $validated['bank_reference'],
                'remarks' => $validated['remarks'],
                'status' => 'cleared',
            ]);

            $invoice->update(['status' => 'paid']);

            DB::commit();

            return response()->json([
                'success' => true,
                'payment' => array_merge($payment->toArray(), [
                    'paid_date' => $payment->paid_date->format('Y-m-d'),
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to process payment.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // SUPPLIER PORTAL: SUBMIT QUOTATION RESPONSE
    // (Called from supplier-side auth routes)
    // ──────────────────────────────────────────────────────────────

    public function submitQuotationResponse(Request $request, $rfqId)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'lead_time' => 'required|string',
            'validity_date' => 'required|date|after:today',
            'payment_terms' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $rfq = RequestForQuotation::findOrFail($rfqId);
            $supplier = Supplier::findOrFail($validated['supplier_id']);

            $response = RfqResponse::create([
                'rfq_id' => $rfq->id,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->business_name,
                'unit_price' => $validated['unit_price'],
                'total_price' => $validated['total_price'],
                'lead_time' => $validated['lead_time'],
                'validity_date' => $validated['validity_date'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'],
                'status' => 'pending_review',
                'submitted_at' => now(),
            ]);

            // Update RFQ status
            $existingResponses = RfqResponse::where('rfq_id', $rfq->id)->count();
            $rfq->update(['status' => $existingResponses >= 2 ? 'responded' : 'partial_response']);

            return response()->json(['success' => true, 'response' => $response]);
        } catch (\Exception $e) {
            Log::error('Submit quotation response failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to submit quotation.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // SUPPLIER PORTAL: SEND PURCHASE INVOICE
    // (Supplier submits invoice after PO is confirmed/received)
    // ──────────────────────────────────────────────────────────────

    public function receiveInvoiceFromSupplier(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|exists:scm_purchase_orders,id',
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'amount' => 'required|numeric|min:0.01',
            'payment_terms' => 'required|string',
        ]);

        try {
            $po = ScmPurchaseOrder::findOrFail($validated['po_id']);

            $invoice = PurchaseInvoice::create([
                'invoice_number' => $validated['invoice_number'],
                'po_id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_id' => $po->supplier_id,
                'supplier_name' => $po->supplier_name,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'amount' => $validated['amount'],
                'payment_terms' => $validated['payment_terms'],
                'status' => 'unpaid',
                'received_at' => now(),
            ]);

            return response()->json(['success' => true, 'invoice' => $invoice]);
        } catch (\Exception $e) {
            Log::error('Receive invoice failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to record invoice.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // HELPERS: Auto-numbering
    // ──────────────────────────────────────────────────────────────

    private function generateRFQNumber(): string
    {
        $year = now()->format('Y');
        $count = RequestForQuotation::whereYear('created_at', $year)->count() + 1;

        return 'RFQ-'.$year.'-'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    private function generatePONumber(): string
    {
        $year = now()->format('Y');
        $count = ScmPurchaseOrder::whereYear('created_at', $year)->count() + 1;

        return 'SCMPO-'.$year.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber(): string
    {
        $year = now()->format('Y');
        $count = ProcurementPayment::whereYear('created_at', $year)->count() + 1;

        return 'PAY-'.$year.'-'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Scm\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScmManagerController extends Controller
{
    /**
     * Forward a material request to the Procurement module (PRO).
     */
    public function forwardMaterialRequest(Request $request, $id)
    {
        try {
            $materialRequest = MaterialRequest::findOrFail($id);

            if ($materialRequest->status !== 'pending') {
                return redirect()->back()->withErrors(['error' => 'Material request already processed.']);
            }

            $materialRequest->update([
                'status' => 'forwarded',
                'forwarded_at' => now(),
                'forwarded_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Material request forwarded to Procurement.');
        } catch (\Exception $e) {
            Log::error('Forward material request failed: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to forward request.']);
        }
    }

    /**
     * Approve an order for manufacturing after INV has confirmed sufficient materials.
     * Moves the order from inv_checked (with sufficient flag true) to man_production.
     */
    public function approveManufacturing(PurchaseOrder $order)
    {
        $queue = $order->queue;

        if (! $queue) {
            return redirect()->back()->withErrors(['error' => 'Order queue not found.']);
        }

        if ($queue->stage !== 'inv_checked') {
            return redirect()->back()->withErrors(['error' => 'Order is not in the correct stage for manufacturing approval.']);
        }

        if (! $queue->inv_check_sufficient) {
            return redirect()->back()->withErrors(['error' => 'Order materials are insufficient. Please handle material requests first.']);
        }

        $queue->update(['stage' => 'man_production', 'man_started_at' => now()]);

        return redirect()->back()->with('success', "Order {$order->po_number} sent to Manufacturing.");
    }
}

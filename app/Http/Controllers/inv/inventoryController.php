<?php

namespace App\Http\Controllers\inv;

use App\Http\Controllers\Controller;
use App\Models\inv\Material;
use App\Models\inv\Warehouse;
use App\Models\inv\WarehouseMaterial;
use App\Models\Scm\ScmPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InventoryController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function warehouseColors(): array
    {
        return ['blue', 'emerald', 'amber', 'violet', 'rose', 'cyan'];
    }

    private function buildInventoryData(): array
    {
        $warehouses = Warehouse::all();
        $data = [];

        foreach ($warehouses as $wh) {
            $items = WarehouseMaterial::with('material')
                ->where('warehouse_id', $wh->id)
                ->get()
                ->map(function (WarehouseMaterial $wm) {
                    $mat = $wm->material;
                    $qty = (float) $wm->quantity;
                    $reorder = $mat->reorder_point;

                    if ($qty <= 0) {
                        $status = 'Out of Stock';
                    } elseif ($qty <= $reorder) {
                        $status = 'Low Stock';
                    } else {
                        $status = 'In Stock';
                    }

                    return [
                        'wm_id' => $wm->id,
                        'id' => $mat->mat_id,
                        'material_id' => $mat->id,
                        'name' => $mat->name,
                        'category' => $mat->category,
                        'qty' => $qty,
                        'unit' => $mat->unit,
                        'reorder' => $reorder,
                        'cost' => (float) $mat->unit_cost,
                        'status' => $status,
                    ];
                })
                ->values()
                ->toArray();

            $data[$wh->id] = $items;
        }

        return $data;
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    public function inventory()
    {
        $warehouses = Warehouse::all()->map(fn ($w) => [
            'id' => $w->id,
            'name' => $w->name,
            'location' => $w->location,
            'manager' => $w->manager,
            'color' => $w->color,
        ])->values()->toArray();

        $inventoryData = $this->buildInventoryData();

        $masterMaterials = Material::orderBy('name')->get()->map(fn ($m) => [
            'id' => $m->id,
            'mat_id' => $m->mat_id,
            'name' => $m->name,
            'category' => $m->category,
            'unit' => $m->unit,
            'cost' => (float) $m->unit_cost,
        ])->values()->toArray();

        // FETCH INCOMING DELIVERIES (Filters out fully received items)
        $incomingDeliveries = ScmPurchaseOrder::with('items')
            ->whereIn('status', ['shipping', 'delivered', 'partially_received'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($po) {
                // Calculate pending quantities for each item in the PO
                $pendingItems = $po->items->map(function ($item) {
                    $received = (float) ($item->received_qty ?? 0);
                    $pending = max(0, (float) $item->qty - $received);

                    return [
                        'id' => $item->id,
                        'material_id' => $item->material_id,
                        'material_name' => $item->material_name,
                        'qty' => $item->qty, // Originally Ordered
                        'received_qty' => $received, // Total Received Historically
                        'pending_qty' => $pending, // Left to Receive NOW
                        'unit' => $item->unit,
                    ];
                })->filter(fn ($item) => $item['pending_qty'] > 0)->values(); // Only show items that still need receiving

                return [
                    'id' => $po->id,
                    'po_number' => $po->po_number,
                    'supplier_name' => $po->supplier_name,
                    'status' => $po->status,
                    'items' => $pendingItems,
                ];
            })
            ->filter(fn ($po) => count($po['items']) > 0) // Only send POs that still have pending items
            ->values();

        return Inertia::render('Dashboard/INV/Manager/inventory', [
            'warehouses' => $warehouses,
            'inventoryData' => $inventoryData,
            'masterMaterials' => $masterMaterials,
            'incomingDeliveries' => $incomingDeliveries,
        ]);
    }

    // ── Receive Incoming Deliveries (Handles Partials) ────────────────────────

    public function receiveDelivery(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|exists:scm_purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:scm_purchase_order_items,id',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.received_qty' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $po = ScmPurchaseOrder::with('items')->findOrFail($validated['po_id']);
            $hasReceivedSomething = false;

            foreach ($validated['items'] as $itemData) {
                $rcvd = (float) $itemData['received_qty'];

                if ($rcvd > 0) {
                    $hasReceivedSomething = true;

                    // 1. Add Stock to the Physical Warehouse
                    $wm = WarehouseMaterial::firstOrNew([
                        'warehouse_id' => $validated['warehouse_id'],
                        'material_id' => $itemData['material_id'],
                    ]);
                    $wm->quantity = ($wm->quantity ?? 0) + $rcvd;
                    $wm->save();

                    // 2. Add 'Received Quantity' to the Purchase Order Item History
                    $poItem = $po->items->where('id', $itemData['item_id'])->first();
                    if ($poItem) {
                        $poItem->received_qty = ($poItem->received_qty ?? 0) + $rcvd;
                        $poItem->save();
                    }
                }
            }

            // 3. Determine if the PO is FULLY received or just PARTIALLY received
            $po->refresh();
            $allFullyReceived = true;

            foreach ($po->items as $item) {
                if (($item->received_qty ?? 0) < $item->qty) {
                    $allFullyReceived = false;
                    break;
                }
            }

            if ($allFullyReceived) {
                $po->update(['status' => 'received']);
            } elseif ($hasReceivedSomething) {
                $po->update(['status' => 'partially_received']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Delivery received and inventory updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receiving failed: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to process receiving.']);
        }
    }

    // ── Store Warehouse ───────────────────────────────────────────────────────

    public function storeWarehouse(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager' => 'nullable|string|max:255',
        ]);

        $colors = self::warehouseColors();
        $data['color'] = $colors[Warehouse::count() % count($colors)];

        Warehouse::create($data);

        return redirect()->back()->with('success', 'Warehouse created.');
    }

    // ── Add Item to Warehouse ─────────────────────────────────────────────────

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        $wm = WarehouseMaterial::firstOrNew([
            'warehouse_id' => $data['warehouse_id'],
            'material_id' => $data['material_id'],
        ]);

        $wm->quantity = $wm->quantity + (float) $data['quantity'];
        $wm->save();

        return redirect()->back()->with('success', 'Item added to warehouse.');
    }

    // ── Remove Item from Warehouse ────────────────────────────────────────────

    public function destroyItem(int $wmId)
    {
        WarehouseMaterial::findOrFail($wmId)->delete();

        return redirect()->back()->with('success', 'Item removed.');
    }
}

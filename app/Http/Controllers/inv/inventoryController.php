<?php

namespace App\Http\Controllers\inv;

use App\Http\Controllers\Controller;
use App\Models\inv\Material;
use App\Models\inv\Warehouse;
use App\Models\inv\WarehouseMaterial;
use Illuminate\Http\Request;
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
                        'id' => $mat->mat_id,   // displayed SKU
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

        // Master materials list (for the Add-Item dropdown)
        $masterMaterials = Material::orderBy('name')->get()->map(fn ($m) => [
            'id' => $m->id,
            'mat_id' => $m->mat_id,
            'name' => $m->name,
            'category' => $m->category,
            'unit' => $m->unit,
            'cost' => (float) $m->unit_cost,
        ])->values()->toArray();

        return Inertia::render('Dashboard/INV/Manager/inventory', [
            'warehouses' => $warehouses,
            'inventoryData' => $inventoryData,
            'masterMaterials' => $masterMaterials,
        ]);
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

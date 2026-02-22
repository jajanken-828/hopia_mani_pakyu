<?php

namespace App\Http\Controllers\eco\manager;

use App\Http\Controllers\Controller;
use App\Models\PricingTier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display the Pricing Architect dashboard.
     * Fetches existing tiers and orders awaiting tier assignment.
     */
    public function book()
    {
        return Inertia::render('Dashboard/ECO/Manager/book', [
            // Returns the list of tiers for the management table
            'priceBooks' => [
                'data' => PricingTier::orderBy('min_bulk_quantity', 'desc')->get(),
            ],
            // Orders that passed ECO Manager credit review and need pricing
            'pendingTiering' => PurchaseOrder::with(['client', 'items'])
                ->where('status', 'tier_assignment')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Store a new pricing tier threshold (e.g., 1000+ for Gold).
     * Connects to the 'New Tier' modal in your Vue frontend.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_bulk_quantity' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        PricingTier::create($validated);

        return back()->with('success', "New tier '{$request->name}' successfully configured.");
    }

    /**
     * Workflow Step: Analyze order quantity and apply the matching tier.
     * Fixed: Correctly calculates discount_amount using DB tier rules.
     */
    public function applyTier(Request $request, PurchaseOrder $po)
    {
        // 1. Calculate total items in the bulk order
        $totalItems = $po->items->sum('quantity');

        // 2. Find matching tier based on quantity rules from the database
        // Finds the highest qualified tier (e.g., 1000+ for Gold, 100+ for Silver)
        $tier = PricingTier::where('min_bulk_quantity', '<=', $totalItems)
            ->where('status', 1) // Ensures the tier is active
            ->orderBy('min_bulk_quantity', 'desc')
            ->first();

        // 3. Define the values based on the tier found (Default to Normal/0% if none)
        $tierName = $tier ? $tier->name : 'Normal';
        $discountPercent = $tier ? (float) $tier->discount_percentage : 0;

        // 4. Calculate actual money amounts
        $subtotal = (float) $po->subtotal;
        $discountAmount = $subtotal * ($discountPercent / 100);
        $finalTotal = $subtotal - $discountAmount;

        // 5. Update the Order and move to Client Approval stage
        $po->update([
            'tier_level' => $tierName,
            'discount_amount' => $discountAmount,
            'total_amount' => $finalTotal,
            'status' => 'pending_client_approval', // Moves back to client dashboard for acceptance
            'notes' => "Automated Tiering: Applied {$tierName} (-{$discountPercent}%) based on {$totalItems} items.",
        ]);

        return back()->with('success', "Tier {$tierName} (-{$discountPercent}%) applied. Quotation sent to client.");
    }
}

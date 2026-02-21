<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the B2B Storefront with real product data.
     */
    public function index()
    {
        // Fetch published products from the real database
        $products = Product::where('status', 'published')
            ->latest()
            ->get();

        return Inertia::render('CLIENT/dashboard', [
            'products' => $products,
            'stats' => [
                'pending_orders' => 0, // Placeholder for future Order model logic
                'completed_orders' => 0,
                'recent_orders' => [],
            ],
        ]);
    }
}

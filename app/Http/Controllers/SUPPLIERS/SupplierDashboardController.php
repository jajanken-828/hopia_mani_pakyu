<?php

namespace App\Http\Controllers\SUPPLIERS; // Ensure this matches your folder name

use App\Http\Controllers\Controller; // Required to extend the base Controller
use Inertia\Inertia;

class SupplierDashboardController extends Controller
{
    /**
     * Display the supplier dashboard.
     */
    public function index()
    {
        // Renders the component at resources/js/Pages/SUPPLIER/supplierDashboard.vue
        return Inertia::render('SUPPLIER/supplierDashboard', [
            'stats' => [
                'activeOrders' => 12,
                'pendingInvoices' => 4,
                'totalShipments' => 158,
                'qualityRating' => '98%',
            ],
        ]);
    }
}

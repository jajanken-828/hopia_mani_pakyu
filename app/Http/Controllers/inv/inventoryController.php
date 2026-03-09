<?php

namespace App\Http\Controllers\inv;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function inventory()
    {
        return Inertia::render('Dashboard/INV/Manager/inventory');
    }
}

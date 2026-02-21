<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class InvoicesController extends Controller
{
    public function invoices()
    {
        // TODO: Replace dummy stats with actual DB queries
        return Inertia::render('CLIENT/invoices');
    }
}

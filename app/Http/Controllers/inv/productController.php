<?php

namespace App\Http\Controllers\inv;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function product()
    {
        return Inertia::render('Dashboard/INV/Manager/product');
    }
}

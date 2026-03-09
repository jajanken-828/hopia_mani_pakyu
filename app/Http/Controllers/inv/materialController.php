<?php

namespace App\Http\Controllers\inv;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MaterialController extends Controller
{
    public function material()
    {
        return Inertia::render('Dashboard/INV/Manager/material');
    }
}

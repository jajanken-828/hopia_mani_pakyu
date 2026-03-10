<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function vendor()
    {
        return Inertia::render('Dashboard/SCM/Manager/vendor');
    }
}

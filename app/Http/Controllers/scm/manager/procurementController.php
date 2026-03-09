<?php

namespace App\Http\Controllers\scm\manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProcurementController extends Controller
{
    public function procurement()
    {
        return Inertia::render('Dashboard/SCM/Manager/procurement');
    }
}

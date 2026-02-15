<?php

namespace App\Http\Controllers\hrm\employee;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HrmstaffpayrollController extends Controller
{
    public function hrmstaffpayroll()
    {
        return Inertia::render('Dashboard/HRM/Employee/hrmstaffpayroll');
    }
}

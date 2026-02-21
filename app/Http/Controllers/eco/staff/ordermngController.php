<?php

namespace App\Http\Controllers\eco\staff;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class OrdermngController extends Controller
{
    public function ordermng()
    {
        return Inertia::render('Dashboard/ECO/Employee/ordermng');
    }
}

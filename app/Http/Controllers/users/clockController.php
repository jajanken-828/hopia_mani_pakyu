<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ClockController extends Controller
{
    public function clock()
    {
        return Inertia::render('Dashboard/USERS/clock');
    }
}

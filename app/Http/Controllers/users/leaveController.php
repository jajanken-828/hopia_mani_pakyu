<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function leave()
    {
        return Inertia::render('Dashboard/USERS/leave');
    }
}

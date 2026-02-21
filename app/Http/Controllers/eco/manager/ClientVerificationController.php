<?php

namespace App\Http\Controllers\eco\manager;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientVerificationController extends Controller
{
    public function index()
    {
        // Path MUST match your screenshot: Dashboard/ECO/Manager/index
        return Inertia::render('Dashboard/ECO/Manager/index', [
            'pendingCompanies' => Client::where('status', 'pending')->latest()->get(),
            'verifiedCompanies' => Client::whereIn('status', ['active', 'suspended'])->latest()->get(),
        ]);
    }

    public function updateStatus(Request $request, Client $client)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,rejected',
        ]);

        $client->update([
            'status' => $request->status,
        ]);

        return back()->with('success', "Status updated to {$request->status}.");
    }
}

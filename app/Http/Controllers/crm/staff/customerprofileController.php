<?php

namespace App\Http\Controllers\crm\staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmInteraction;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerprofileController extends Controller
{
    /**
     * Display the Partner Ecosystem Grid.
     * Fetches all clients and details for a selected client.
     */
    public function customerprofile($id = null)
    {
        // 1. Fetch ALL clients for the ecosystem grid
        $allClients = Client::all();

        // 2. Identify the specific client to view details for
        // Default to the first client if no ID is provided
        $selectedClient = $id ? Client::find($id) : Client::first();

        // 3. Return data to the Vue frontend
        return Inertia::render('Dashboard/CRM/Employee/customerprofile', [
            'customers' => $allClients, // Required for the loop in your Vue file
            'customer' => $selectedClient,
            'interactionHistory' => $selectedClient
                ? CrmInteraction::where('contactable_id', $selectedClient->id)
                    ->where('contactable_type', Client::class)
                    ->with('user:id,name')
                    ->latest()
                    ->get()
                : [],
            'liveProduction' => $selectedClient
                ? PurchaseOrder::where('client_id', $selectedClient->id)
                    ->whereNotIn('status', ['approved', 'rejected'])
                    ->get()
                : [],
        ]);
    }

    public function storeInteraction(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:Call,Email,Meeting,System',
            'note' => 'required|string|max:1000',
        ]);

        CrmInteraction::create([
            'contactable_id' => $validated['client_id'],
            'contactable_type' => Client::class,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'note' => $validated['note'],
        ]);

        return back()->with('message', 'Interaction successfully recorded.');
    }
}

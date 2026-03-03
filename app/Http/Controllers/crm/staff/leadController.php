<?php

namespace App\Http\Controllers\crm\staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CreditAccount;
use App\Models\CrmLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function lead()
    {
        return Inertia::render('Dashboard/CRM/Employee/lead', [
            // Only fetch leads for the logged-in staff
            'leads' => CrmLead::where('assigned_staff_id', Auth::id())->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'interest_fabric' => 'required|string',
            'estimated_value' => 'required|numeric|min:0',
        ]);

        CrmLead::create([
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'interest_fabric' => $validated['interest_fabric'],
            'estimated_value' => $validated['estimated_value'],
            'status' => 'Inquiry',
            'assigned_staff_id' => Auth::id(),
        ]);

        return back()->with('message', 'New deal created and added to pipeline.');
    }

    public function updateStatus(Request $request, $id)
    {
        $lead = CrmLead::findOrFail($id);
        $lead->update(['status' => $request->status]);

        return back();
    }

    /**
     * Convert a Lead to an Official Business Client
     */
    public function convertToClient(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:crm_leads,id',
            'company_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'tin_number' => 'required|string|unique:clients,tin_number',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string',
            'company_address' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create the Client record in the clients table
            $client = Client::create([
                'company_name' => $validated['company_name'],
                'business_type' => $validated['business_type'],
                'tin_number' => $validated['tin_number'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company_address' => $validated['company_address'],
                'password' => bcrypt($validated['password']),
                'status' => 'active',
            ]);

            // 2. Create mandatory Credit Account for the new client
            CreditAccount::create([
                'client_id' => $client->id,
                'outstanding_balance' => 0.00,
                'is_good_payer' => 1,
            ]);

            // 3. Update status to 'Converted' so it disappears from 'Closed-Won' in the UI
            $lead = CrmLead::find($validated['lead_id']);
            $lead->update(['status' => 'Converted']);

            DB::commit();

            return back()->with('message', 'Partnership established! Lead has been moved out of the pipeline.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to convert lead: '.$e->getMessage()]);
        }
    }
}

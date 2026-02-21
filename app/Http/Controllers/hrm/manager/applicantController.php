<?php

namespace App\Http\Controllers\hrm\manager;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    /**
     * Display a listing of applicants for Staff and Managers.
     */
    public function index()
    {
        return Inertia::render('Dashboard/HRM/Applicants/application', [
            'applicants' => Applicant::with('interview')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'first_name' => $a->first_name,
                    'last_name' => $a->last_name,
                    'email' => $a->email,
                    'phone_number' => $a->phone_number,
                    'position_applied' => $a->position_applied,
                    'status' => $a->status,
                    'created_at' => $a->created_at,
                    'notice_period' => $a->notice_period,
                    'street_address' => $a->street_address,
                    'city' => $a->city,
                    'state_province' => $a->state_province,
                    'postal_zip_code' => $a->postal_zip_code,
                    'sss_file_url' => $a->sss_file ? Storage::url($a->sss_file) : null,
                    'philhealth_file_url' => $a->philhealth_file ? Storage::url($a->philhealth_file) : null,
                    'pagibig_file_url' => $a->pagibig_file ? Storage::url($a->pagibig_file) : null,
                    'has_interview' => $a->interview !== null,
                ]),
        ]);
    }

    /**
     * Store a newly created applicant.
     * Works for both Public "Apply Now" and Internal HRM "Add Applicant".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email',
            'phone_number' => 'required|string',
            'position_applied' => 'required|string',
            'expected_salary' => 'nullable|numeric',
            'notice_period' => 'required|string|in:immediate,15_days,30_days,60_days',
            'street_address' => 'required|string',
            'street_address_line2' => 'nullable|string',
            'city' => 'required|string',
            'state_province' => 'required|string',
            'postal_zip_code' => 'required|string',
            // Adjusted to nullable to support public form if experience isn't a radio choice there
            'textile_experience' => 'nullable|string',
            'status' => 'nullable|string',
            // File validation
            'sss_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'philhealth_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pagibig_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Internal Only Fields (Scheduling)
            'scheduled_at' => 'nullable|date',
            'interview_type' => 'nullable|string|in:phone,technical,behavioral,onsite,video',
            'duration' => 'nullable|integer|in:15,30,45,60',
            'interviewer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Data for the Applicant Model
            $data = $request->only([
                'first_name', 'middle_name', 'last_name', 'email', 'phone_number',
                'position_applied', 'expected_salary', 'notice_period', 'street_address',
                'street_address_line2', 'city', 'state_province', 'postal_zip_code',
                'textile_experience',
            ]);

            // Default status to 'Pending' for new entries
            $data['status'] = $request->status ?? 'Pending';

            // Handle Government ID Uploads
            if ($request->hasFile('sss_file')) {
                $data['sss_file'] = $request->file('sss_file')->store('applicants/ids', 'public');
            }
            if ($request->hasFile('philhealth_file')) {
                $data['philhealth_file'] = $request->file('philhealth_file')->store('applicants/ids', 'public');
            }
            if ($request->hasFile('pagibig_file')) {
                $data['pagibig_file'] = $request->file('pagibig_file')->store('applicants/ids', 'public');
            }

            $applicant = Applicant::create($data);

            // Handle Internal Interview Scheduling (if HRM user provided it in the modal)
            if ($request->filled('scheduled_at')) {
                Interview::create([
                    'applicant_id' => $applicant->id,
                    'scheduled_at' => $request->scheduled_at,
                    'interview_type' => $request->interview_type,
                    'duration' => $request->duration ?? 45,
                    'interviewer' => $request->interviewer,
                    'location' => $request->location,
                    'notes' => $request->notes,
                ]);

                // If scheduled immediately, update status to Interview
                $applicant->update(['status' => 'Interview']);
            }

            return back()->with('success', 'Applicant registered successfully.');
        });
    }

    /**
     * Update the stage/status of an applicant (Pipeline Management).
     */
    public function updateStage(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        // Map frontend pipeline stages to database status values
        $statusMap = [
            'FOR INTERVIEW' => 'Interview',
            'FINAL INTERVIEW' => 'final',
            'ONBOARDING' => 'onboard',
            'Rejected' => 'Rejected',
        ];

        if (isset($validated['status']) && array_key_exists($validated['status'], $statusMap)) {
            $validated['status'] = $statusMap[$validated['status']];
        }

        $applicant->update($validated);

        return back()->with('success', 'Applicant stage updated.');
    }

    /**
     * Promote an Applicant to a Trainee System User.
     */
    public function createUser(Request $request, $id)
    {
        $applicant = Applicant::findOrFail($id);

        $request->validate([
            'role' => 'required|string',
            'position' => 'required|string',
        ]);

        return DB::transaction(function () use ($applicant, $request) {
            $year = now()->year;

            User::create([
                'name' => $applicant->first_name.' '.$applicant->last_name,
                'email' => $applicant->email,
                'password' => Hash::make('password'),
                'role' => $request->role,
                'position' => $request->position,
                'department' => $request->role,
                'join_date' => now(),
                'is_active' => true,
                'employee_id' => 'MONTI-'.$year.'-'.$request->role.'-'.str_pad(User::where('role', $request->role)->count() + 1, 4, '0', STR_PAD_LEFT),
            ]);

            // Complete the applicant cycle
            $applicant->update(['status' => 'Account Created']);

            return back()->with('success', 'User account created successfully.');
        });
    }

    /**
     * Schedule an interview for an existing applicant in the pipeline.
     */
    public function scheduleInterview(Request $request, Applicant $applicant)
    {
        $request->validate([
            'scheduled_at' => 'required|date',
            'interview_type' => 'required|string|in:phone,technical,behavioral,onsite,video',
            'duration' => 'nullable|integer|in:15,30,45,60',
            'interviewer' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $applicant->update(['status' => 'Interview']);

        Interview::create([
            'applicant_id' => $applicant->id,
            'scheduled_at' => $request->scheduled_at,
            'interview_type' => $request->interview_type,
            'duration' => $request->duration ?? 45,
            'interviewer' => $request->interviewer,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Interview scheduled successfully.');
    }
}

<?php

namespace App\Http\Controllers\hrm;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TraineeGrade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HrmDashboardController extends Controller
{
    // New Dashboard with Charts
    public function managerDashboard()
    {
        // Basic stats
        $totalEmployees = User::whereIn('position', ['manager', 'staff'])->count();
        $totalTrainees = User::where('position', 'trainee')->count();
        $activeEmployees = User::where('is_active', true)->count();
        $pendingLeaves = 0; // Replace with actual leave count if implemented
        $attendanceRate = 95; // Placeholder

        // Department breakdown (active employees)
        $departments = ['HRM', 'SCM', 'FIN', 'MAN', 'INV', 'ORD', 'WAR', 'CRM', 'ECO', 'PRO', 'PROJ', 'IT'];
        $departmentCounts = [];
        foreach ($departments as $dept) {
            $departmentCounts[$dept] = User::where('role', $dept)
                ->where('position', '!=', 'trainee')
                ->where('is_active', true)
                ->count();
        }

        // Attendance trend (last 6 months) – placeholder data
        $months = [];
        $attendanceTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('M');
            $attendanceTrend[] = rand(92, 98); // Random placeholder
        }

        // Recruitment pipeline (placeholder)
        $recruitmentPipeline = [
            'applications' => 45,
            'interviews' => 12,
            'offers' => 5,
            'hired' => 3,
        ];

        return Inertia::render('Dashboard/HRM/Manager/Index', [
            'stats' => [
                'totalEmployees' => $totalEmployees,
                'activeEmployees' => $activeEmployees,
                'totalTrainees' => $totalTrainees,
                'pendingLeaves' => $pendingLeaves,
                'attendanceRate' => $attendanceRate,
            ],
            'departmentCounts' => $departmentCounts,
            'attendanceTrend' => [
                'months' => $months,
                'values' => $attendanceTrend,
            ],
            'recruitmentPipeline' => $recruitmentPipeline,
        ]);
    }

    // Employee Management (old dashboard content)
    public function employeeManagement()
    {
        $suggestedTrainees = User::where('position', 'trainee')
            ->with('traineeGrade')
            ->orderByRaw('promotion_suggested DESC')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'employee_id' => $user->employee_id,
                    'suggested_at' => $user->suggested_at,
                    'promotion_suggested' => (bool) $user->promotion_suggested,
                    'profile_photo_url' => $user->profile_photo_path
                        ? asset('storage/'.$user->profile_photo_path)
                        : null,
                    'trainee_grade' => $user->traineeGrade ? [
                        'id' => $user->traineeGrade->id,
                        'skills_performance' => $user->traineeGrade->skills_performance,
                        'behaviour' => $user->traineeGrade->behaviour,
                        'technicals' => $user->traineeGrade->technicals,
                        'safety_awareness' => $user->traineeGrade->safety_awareness,
                        'productivity' => $user->traineeGrade->productivity,
                        'total_percentage' => $user->traineeGrade->total_percentage,
                    ] : null,
                ];
            });

        $allEmployees = User::with(['auditLogs' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->whereIn('position', ['manager', 'staff'])
            ->orderBy('role')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'position' => $user->position,
                    'department' => $user->department,
                    'employee_id' => $user->employee_id,
                    'is_active' => (bool) $user->is_active,
                    'status' => $user->status ?? ($user->is_active ? 'Active' : 'Inactive'),
                    'join_date' => $user->join_date,
                    'audit_logs' => $user->auditLogs,
                    'profile_photo_url' => $user->profile_photo_path
                        ? asset('storage/'.$user->profile_photo_path)
                        : null,
                ];
            });

        return Inertia::render('Dashboard/HRM/Manager/Employee', [
            'suggestedTrainees' => $suggestedTrainees,
            'allEmployees' => $allEmployees,
            'stats' => [
                'totalEmployees' => User::whereIn('position', ['manager', 'staff'])->count(),
                'activeRecruitment' => 12,
                'pendingLeaves' => 8,
                'attendanceRate' => 95,
                'totalTrainees' => User::where('position', 'trainee')->count(),
            ],
        ]);
    }

    // Staff dashboard remains unchanged
    public function staffDashboard()
    {
        $employees = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'position' => $user->position,
                'is_active' => $user->is_active,
                'department' => $user->department,
                'employee_id' => $user->employee_id,
                'profile_photo_path' => $user->profile_photo_path,
                'profile_photo_url' => $user->profile_photo_path
                    ? asset('storage/'.$user->profile_photo_path)
                    : null,
            ];
        });

        $auditLogs = AuditLog::orderBy('created_at', 'desc')->take(50)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'target_name' => $log->target_name,
                'action' => $log->action,
                'reason' => $log->reason,
                'created_at' => $log->created_at->format('M d, Y h:i A'),
            ];
        });

        return Inertia::render('Dashboard/HRM/Employee/Index', [
            'employees' => $employees,
            'auditLogs' => $auditLogs,
            'stats' => [
                'total' => User::count(),
                'present' => User::where('is_active', true)->count(),
                'on_leave' => 0,
                'assignedTasks' => 4,
                'leaveBalance' => 15,
                'trainingModules' => 2,
            ],
            'user' => Auth::user(),
        ]);
    }

    // Promotion and grading methods remain unchanged
    public function finalizePromotion($id)
    {
        $trainee = User::findOrFail($id);

        $trainee->update([
            'position' => 'staff',
            'promotion_suggested' => false,
            'suggested_at' => null,
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'target_id' => $trainee->id,
            'target_name' => $trainee->name,
            'action' => 'promote',
            'reason' => 'Manager approved promotion from Trainee to Staff.',
        ]);

        return redirect()->back()->with('message', "{$trainee->name} has been successfully promoted to Staff.");
    }

    public function gradeAndPromote(Request $request, $id)
    {
        $validated = $request->validate([
            'skills_performance' => 'required|integer|min:1|max:5',
            'behaviour' => 'required|integer|min:1|max:5',
            'technicals' => 'required|integer|min:1|max:5',
            'safety_awareness' => 'required|integer|min:1|max:5',
            'productivity' => 'required|integer|min:1|max:5',
        ]);

        $trainee = User::findOrFail($id);

        $totalStars = (int) $validated['skills_performance']
            + (int) $validated['behaviour']
            + (int) $validated['technicals']
            + (int) $validated['safety_awareness']
            + (int) $validated['productivity'];

        $percentage = (int) round(($totalStars / 25) * 100);

        try {
            DB::transaction(function () use ($trainee, $validated, $percentage) {
                $grade = TraineeGrade::firstOrNew(['user_id' => $trainee->id]);
                $grade->skills_performance = (int) $validated['skills_performance'];
                $grade->behaviour = (int) $validated['behaviour'];
                $grade->technicals = (int) $validated['technicals'];
                $grade->safety_awareness = (int) $validated['safety_awareness'];
                $grade->productivity = (int) $validated['productivity'];
                $grade->total_percentage = $percentage;
                $grade->save();

                if ($percentage >= 80) {
                    $trainee->position = 'staff';
                    $trainee->promotion_suggested = false;
                    $trainee->suggested_at = null;
                    $trainee->save();
                }
            });

            AuditLog::create([
                'admin_id' => Auth::id(),
                'target_id' => $trainee->id,
                'target_name' => $trainee->name,
                'action' => $percentage >= 80 ? 'promote' : 'grade',
                'reason' => $percentage >= 80
                    ? "Manager graded and auto-promoted. Final score: {$percentage}%."
                    : "Manager submitted grade. Score: {$percentage}%. Below 80% threshold.",
            ]);
        } catch (\Throwable $e) {
            \Log::error('Grading failed: '.$e->getMessage());

            return redirect()->back()->withErrors([
                'grade' => 'Failed to save grades: '.$e->getMessage(),
            ]);
        }

        if ($percentage >= 80) {
            return redirect()->back()->with(
                'message',
                "{$trainee->name} scored {$percentage}% and has been automatically promoted to Staff!"
            );
        }

        return redirect()->back()->with(
            'message',
            "{$trainee->name} has been graded. Score: {$percentage}% — below the 80% promotion threshold."
        );
    }
}

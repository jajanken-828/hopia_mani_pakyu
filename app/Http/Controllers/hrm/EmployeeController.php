<?php

namespace App\Http\Controllers\hrm;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display employee directory and trainee list.
     */
    public function index()
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

    /**
     * Update an employee's information.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:HRM,SCM,FIN,MAN,INV,ORD,WAR,CRM,ECO,PRO,PROJ,IT',
            'position' => 'required|in:manager,staff,trainee',
            'department' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $employee = User::findOrFail($id);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'position' => $request->position,
            'department' => $request->department,
            'is_active' => $request->is_active,
            'status' => $request->is_active ? 'Active' : 'Inactive',
        ]);

        return redirect()->back()->with('message', "Information for {$employee->name} updated successfully.");
    }

    /**
     * Toggle employee status (activate/deactivate).
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() == $user->id) {
            return redirect()->back()->with('message', 'System Error: You cannot modify your own account status.');
        }

        $newStatus = $user->is_active ? 0 : 1;
        $action = $newStatus ? 'reactivate' : 'deactivate';

        $user->update([
            'is_active' => $newStatus,
            'status' => $newStatus ? 'Active' : 'Inactive',
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'target_id' => $user->id,
            'target_name' => $user->name,
            'action' => $action,
            'reason' => $request->reason ?? ($newStatus ? 'Account Reactivation' : 'No reason provided'),
        ]);

        $statusText = $newStatus ? 'reactivated' : 'deactivated';

        return redirect()->back()->with('message', "Employee {$user->name} has been successfully {$statusText}.");
    }

    /**
     * Update an employee's role and position.
     * Clears manufacturing_role when the new role is not MAN.
     */
    public function updateRolePosition(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:HRM,SCM,FIN,MAN,INV,ORD,WAR,CRM,ECO,PRO,PROJ,IT,CEO',
            'position' => 'required|in:manager,staff,trainee',
        ]);

        $user = User::findOrFail($id);

        $data = [
            'role' => $request->role,
            'position' => $request->position,
        ];

        // If the user is being moved out of Manufacturing, clear the manufacturing role
        if ($request->role !== 'MAN') {
            $data['manufacturing_role'] = null;
        }

        $user->update($data);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'target_id' => $user->id,
            'target_name' => $user->name,
            'action' => 'role_position_change',
            'reason' => "Role changed to {$request->role}, Position changed to {$request->position}",
        ]);

        return redirect()->back()->with('message', "Employee {$user->name} updated successfully.");
    }
}

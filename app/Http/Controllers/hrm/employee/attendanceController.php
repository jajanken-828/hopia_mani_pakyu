<?php

namespace App\Http\Controllers\hrm\employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\EmployeeShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Display the attendance and shift management page.
     * Updated to handle 12-hour format strings and local timezone context.
     */
    public function attendance(Request $request)
    {
        // Use local timezone for context
        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString();

        // 1. Get month and year from request, or default to current local date
        $month = $request->input('month', $now->month);
        $year = $request->input('year', $now->year);

        // 2. Calculate the dynamic date range for the selected month
        $viewDate = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Manila');
        $startOfMonth = $viewDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $viewDate->copy()->endOfMonth()->toDateString();

        return Inertia::render('Dashboard/HRM/Employee/attendance', [
            'employee_attendance' => User::with(['latestAttendance' => function ($query) use ($today) {
                $query->where('date', $today);
            }, 'currentShift' => function ($query) use ($today) {
                $query->where('effective_date', $today);
            }])->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'dept' => $user->role ?? 'N/A',
                    'shift' => $user->currentShift->shift_type ?? 'Not Set',
                    'clock_in' => $user->latestAttendance->clock_in ?? '---', // Displays formatted string from DB
                    'status' => $user->latestAttendance->status ?? 'Absent',
                ];
            }),

            // 3. Fetch shifts specifically for the month being viewed on the calendar
            'monthly_shifts' => EmployeeShift::whereBetween('effective_date', [$startOfMonth, $endOfMonth])
                ->get()
                ->map(function ($shift) {
                    return [
                        'user_id' => $shift->user_id,
                        'shift_type' => $shift->shift_type,
                        'effective_date' => $shift->effective_date,
                    ];
                }),

            'attendance_status' => [
                'is_clocked_in' => false,
                'last_action' => '08:45 AM',
                'total_hours_today' => '0h 0m',
            ],
        ]);
    }

    /**
     * Handles both single-day updates and monthly bulk scheduling for specific employees.
     */
    public function updateShift(Request $request)
    {
        // 1. Handle Bulk Monthly Scheduling for a Specific Employee
        if ($request->is_bulk) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'shift_type' => 'required|in:Morning,Afternoon,Graveyard',
                'dept_code' => 'required|string',
                'schedule_range' => 'required|string',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer',
            ]);

            $dateContext = Carbon::create($request->year, $request->month, 1, 0, 0, 0, 'Asia/Manila');
            $daysInMonth = $dateContext->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $targetDate = Carbon::create($request->year, $request->month, $day, 0, 0, 0, 'Asia/Manila')->toDateString();

                EmployeeShift::updateOrCreate(
                    ['user_id' => $request->user_id, 'effective_date' => $targetDate],
                    [
                        'shift_type' => $request->shift_type,
                        'dept_code' => $request->dept_code,
                        'schedule_range' => $request->schedule_range,
                    ]
                );
            }

            // Redirect back with current month/year context
            return redirect()->route('hrm.employee.attendance', [
                'month' => $request->month,
                'year' => $request->year,
            ])->with('success', 'Monthly schedule updated for the selected employee.');
        }

        // 2. Handle Single Day Manual Update
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_type' => 'required|in:Morning,Afternoon,Graveyard',
            'effective_date' => 'required|date',
            'schedule_range' => 'required|string',
            'dept_code' => 'nullable|string',
        ]);

        EmployeeShift::updateOrCreate(
            ['user_id' => $request->user_id, 'effective_date' => $request->effective_date],
            [
                'shift_type' => $request->shift_type,
                'dept_code' => $request->dept_code,
                'schedule_range' => $request->schedule_range,
            ]
        );

        // Maintain context for manual updates based on effective_date
        $date = Carbon::parse($request->effective_date)->timezone('Asia/Manila');

        return redirect()->route('hrm.employee.attendance', [
            'month' => $date->month,
            'year' => $date->year,
        ])->with('success', 'Shift updated successfully.');
    }

    /**
     * Toggle logic for local Clock captures.
     * Uses 12-hour format strings to match the new DB schema.
     */
    public function toggle()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Manila'); // Force local time
        $today = $now->toDateString();
        $timeString = $now->format('h:i A'); // 12-hour format

        $log = AttendanceLog::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today]
        );

        if (! $log->clock_in) {
            $status = 'On-Time';
            // Example logic: comparison for morning shift start
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', "$today 08:00:00", 'Asia/Manila');
            if ($now->gt($startTime)) {
                $status = 'Late';
            }

            $log->update([
                'clock_in' => $timeString,
                'status' => $status,
            ]);

            return back()->with('success', "Clocked in at $timeString. Status: $status");
        }

        if (! $log->clock_out) {
            $log->update(['clock_out' => $timeString]);

            return back()->with('success', "Clocked out at $timeString. Shift completed.");
        }

        return back()->with('error', 'You have already completed your shift logs for today.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\TraineeGrade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtoupper($user->role);
        $position = strtolower($user->position);

        // For debugging
        \Log::info('Dashboard access', [
            'user_id' => $user->id,
            'role' => $role,
            'position' => $position,
            'route' => $request->fullUrl(),
        ]);

        /**
         * 1. Priority: Trainee Check
         */
        if ($position === 'trainee') {
            return Inertia::render('Dashboard/TRAINEE/index', [
                'user' => $user,
                'stats' => [
                    'progress' => 45,
                    'assigned_modules' => 5,
                    'days_remaining' => 12,
                ],
            ]);
        }

        /**
         * 2. Department Check
         */
        return match ($role) {
            'HRM' => $this->handleHrmDashboard($position),
            'SCM' => $this->handleScmDashboard($position),
            'FIN' => $this->handleFinDashboard($position),
            'MAN' => $this->handleManDashboard($position),
            'INV' => $this->handleInvDashboard($position),
            'ORD' => $this->handleOrdDashboard($position),
            'WAR' => $this->handleWarDashboard($position),
            'CRM' => $this->handleCrmDashboard($position),
            'ECO' => $this->handleEcoDashboard($position),
            default => $this->renderDefaultDashboard($user),
        };
    }

    private function handleHrmDashboard(string $position)
    {
        if ($position === 'manager') {
            /*
             * Fetch ALL trainees regardless of promotion_suggested flag.
             * The manager must be able to grade any trainee directly —
             * not only those already suggested by HRM Staff.
             *
             * Ordering: suggested trainees first (suggested_at IS NOT NULL),
             * then unsugested ones, both groups sorted by created_at desc.
             */
            $suggestedTrainees = User::where('position', 'trainee')
                ->with('traineeGrade')
                ->orderByRaw('promotion_suggested DESC')   // suggested ones first
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
                        // Explicitly cast to null so Vue computed checks work correctly
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

            // All managers and staff (exclude trainees — they appear in the trainee tab)
            $allEmployees = User::whereIn('position', ['manager', 'staff'])
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
                        'join_date' => $user->join_date,
                        'profile_photo_url' => $user->profile_photo_path
                            ? asset('storage/'.$user->profile_photo_path)
                            : null,
                    ];
                });

            return Inertia::render('Dashboard/HRM/Manager/Index', [
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

        // ── HRM Staff Dashboard ──────────────────────────────────
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

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() == $user->id) {
            return redirect()->back()->with('message', 'You cannot modify your own account status.');
        }

        $newStatus = $user->is_active ? 0 : 1;
        $action = $newStatus ? 'reactivate' : 'deactivate';

        $user->update(['is_active' => $newStatus]);

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
     * Update the specified employee's details.
     */
    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:HRM,SCM,FIN,MAN,INV,ORD,WAR,CRM,ECO',
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
        ]);

        return redirect()->back()->with('message', "Information for {$employee->name} updated successfully.");
    }

    /**
     * Finalize the promotion of a trainee to staff status.
     * Used when the trainee has already been graded by staff.
     * The manager confirms and locks in the promotion.
     */
    public function finalizePromotion($id)
    {
        $trainee = User::findOrFail($id);

        // Update via array is safer than explicit assignment to avoid fillable blocks
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

    /**
     * Grade a trainee directly as an HRM Manager.
     *
     * Scale: each criterion is 1–5 stars. Percentage = (sum / 25) * 100.
     * Passing threshold: 80%. Auto-promotes trainee to Staff if passed.
     *
     * Wrapped in DB::transaction so any failure rolls back atomically
     * and returns a clear error response to the frontend.
     */
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

        // Cast to int — the DB column is INTEGER, not DECIMAL
        $percentage = (int) round(($totalStars / 25) * 100);

        \Log::info('[gradeAndPromote] Starting save', [
            'trainee_id' => $trainee->id,
            'validated' => $validated,
            'totalStars' => $totalStars,
            'percentage' => $percentage,
            'admin_id' => Auth::id(),
        ]);

        try {
            \DB::transaction(function () use ($trainee, $validated, $percentage) {
                // Use explicit firstOrNew + fill + save for the most reliable Eloquent write
                $grade = TraineeGrade::firstOrNew(['user_id' => $trainee->id]);
                $grade->skills_performance = (int) $validated['skills_performance'];
                $grade->behaviour = (int) $validated['behaviour'];
                $grade->technicals = (int) $validated['technicals'];
                $grade->safety_awareness = (int) $validated['safety_awareness'];
                $grade->productivity = (int) $validated['productivity'];
                $grade->total_percentage = $percentage;
                $grade->save();

                \Log::info('[gradeAndPromote] TraineeGrade saved', [
                    'grade_id' => $grade->id,
                    'user_id' => $grade->user_id,
                    'total_percentage' => $grade->total_percentage,
                    'wasRecentlyCreated' => $grade->wasRecentlyCreated,
                ]);

                // Auto-promote if passing threshold
                if ($percentage >= 80) {
                    $trainee->position = 'staff';
                    $trainee->promotion_suggested = false;
                    $trainee->suggested_at = null;
                    $trainee->save();
                }
            });

            // Move AuditLog creation outside the transaction to ensure grades are saved even if audit fails
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
            \Log::error('[gradeAndPromote] DB write failed', [
                'trainee_id' => $trainee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

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

    // ─────────────────────────────────────────────────────────────────
    // Remaining department handlers (unchanged)
    // ─────────────────────────────────────────────────────────────────

    private function handleScmDashboard(string $position)
    {
        if ($position === 'manager') {
            return Inertia::render('Dashboard/SCM/Manager/Index', [
                'stats' => [
                    'totalInventory' => 1250,
                    'pendingOrders' => 8,
                    'completedDeliveries' => 24,
                    'stockAlerts' => 3,
                ],
            ]);
        }

        return Inertia::render('Dashboard/SCM/Employee/Index', [
            'user' => Auth::user(),
            'stats' => [],
        ]);
    }

    private function handleFinDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/FIN/Manager/index' : 'Dashboard/FIN/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'stats' => [
                'totalRevenue' => 0,
                'pendingInvoices' => 0,
                'overduePayments' => 0,
            ],
        ]);
    }

    private function handleManDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/MAN/Manager/index' : 'Dashboard/MAN/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'productionLines' => [],
            'stats' => [
                'activeLines' => 0,
                'dailyOutput' => 0,
                'defectRate' => 0,
            ],
        ]);
    }

    private function handleInvDashboard(string $position)
    {
        $stockLevels = [
            ['id' => 1, 'name' => 'Cotton Fabric',   'sku' => 'CF-001', 'quantity' => 150, 'status' => 'In Stock'],
            ['id' => 2, 'name' => 'Polyester Blend',  'sku' => 'PB-002', 'quantity' => 85,  'status' => 'In Stock'],
            ['id' => 3, 'name' => 'Silk Material',    'sku' => 'SM-003', 'quantity' => 25,  'status' => 'Low Stock'],
            ['id' => 4, 'name' => 'Denim',            'sku' => 'DN-004', 'quantity' => 200, 'status' => 'In Stock'],
            ['id' => 5, 'name' => 'Wool Blend',       'sku' => 'WB-005', 'quantity' => 8,   'status' => 'Critical'],
        ];

        if ($position === 'manager') {
            return Inertia::render('Dashboard/INV/Manager/index', [
                'user' => Auth::user(),
                'stockLevels' => $stockLevels,
                'stats' => [
                    'totalItems' => 468,
                    'lowStock' => 12,
                    'outOfStock' => 3,
                    'warehouseCapacity' => 85,
                ],
            ]);
        }

        return Inertia::render('Dashboard/INV/Employee/index', [
            'user' => Auth::user(),
            'stockLevels' => array_merge($stockLevels, [
                ['id' => 6, 'name' => 'Linen',  'sku' => 'LN-006', 'quantity' => 45, 'status' => 'In Stock'],
                ['id' => 7, 'name' => 'Satin',  'sku' => 'ST-007', 'quantity' => 60, 'status' => 'In Stock'],
                ['id' => 8, 'name' => 'Velvet', 'sku' => 'VL-008', 'quantity' => 15, 'status' => 'Low Stock'],
            ]),
            'stats' => [
                'totalItems' => 468,
                'lowStock' => 12,
                'outOfStock' => 3,
                'warehouseCapacity' => 85,
            ],
        ]);
    }

    private function handleOrdDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/ORD/Manager/index' : 'Dashboard/ORD/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'recentOrders' => [],
            'stats' => [
                'pendingOrders' => 0,
                'completedToday' => 0,
                'totalRevenue' => 0,
            ],
        ]);
    }

    private function handleWarDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/WAR/Manager/index' : 'Dashboard/WAR/Employee/index';

        return Inertia::render($view, [
            'user' => Auth::user(),
            'bins' => [],
            'stats' => [
                'totalBins' => 0,
                'occupiedBins' => 0,
                'availableBins' => 0,
            ],
        ]);
    }

    private function handleCrmDashboard(string $position)
    {
        if ($position === 'manager') {
            $totalLeads = \App\Models\CrmLead::count();
            $wonLeads = \App\Models\CrmLead::where('status', 'Closed-Won')->count();
            $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100) : 0;

            return Inertia::render('Dashboard/CRM/Manager/index', [
                'user' => Auth::user(),
                'stats' => [
                    'totalPipelineValue' => (float) \App\Models\CrmLead::whereNotIn('status', ['Closed-Won', 'Lost'])
                        ->sum('estimated_value'),
                    'activeInquiries' => \App\Models\CrmLead::where('status', 'Inquiry')->count(),
                    'pendingApprovals' => \App\Models\PurchaseOrder::whereIn('status', ['credit_review', 'tier_assignment'])->count(),
                    'conversionRate' => (int) $conversionRate,
                ],
                'dailyActivityCount' => \App\Models\CrmInteraction::whereDate('created_at', Carbon::today())->count(),
                'leaderboard' => User::where('role', 'CRM')
                    ->where('position', 'staff')
                    ->withCount(['leads as won_deals' => fn ($q) => $q->where('status', 'Closed-Won')])
                    ->orderBy('won_deals', 'desc')
                    ->get()
                    ->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'won_deals' => $u->won_deals,
                    ]),
            ]);
        }

        return Inertia::render('Dashboard/CRM/Employee/index', [
            'user' => Auth::user(),
            'stats' => [
                'myLeads' => Auth::user()->leads()->count(),
                'myActivities' => \App\Models\CrmInteraction::where('user_id', Auth::id())->count(),
            ],
        ]);
    }

    private function handleEcoDashboard(string $position)
    {
        $view = $position === 'manager' ? 'Dashboard/ECO/Manager/index' : 'Dashboard/ECO/Employee/index';

        $products = Product::orderBy('name', 'asc')->paginate(10);
        $pendingCompanies = Client::whereIn('status', ['pending', 'Pending'])->latest()->get();
        $verifiedCompanies = Client::whereNotIn('status', ['pending', 'Pending'])->latest()->get();

        $pendingCreditCount = PurchaseOrder::where('status', 'credit_review')->count();
        $pendingTieringCount = PurchaseOrder::where('status', 'tier_assignment')->count();

        $todaySales = PurchaseOrder::where('status', 'approved')->whereDate('created_at', Carbon::today())->sum('total_amount');
        $monthlyRevenue = PurchaseOrder::where('status', 'approved')->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

        $pipelineDetails = PurchaseOrder::with('client')
            ->whereIn('status', ['credit_review', 'tier_assignment'])
            ->latest()
            ->get();

        return Inertia::render($view, [
            'user' => Auth::user(),
            'products' => $products,
            'pendingCompanies' => $pendingCompanies,
            'verifiedCompanies' => $verifiedCompanies,
            'onlineSales' => PurchaseOrder::with('client')->latest()->take(5)->get(),
            'pipelineDetails' => $pipelineDetails,
            'stats' => [
                'todaySales' => number_format($todaySales, 2),
                'monthlyRevenue' => number_format($monthlyRevenue, 2),
                'activeProducts' => Product::where('status', 'published')->count(),
                'lowStockCount' => Product::where('stock', '<', 50)->count(),
                'pendingCredit' => $pendingCreditCount,
                'pendingTiering' => $pendingTieringCount,
            ],
        ]);
    }

    private function renderDefaultDashboard($user)
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_tasks' => 0,
                'pending_tasks' => 0,
                'completed_tasks' => 0,
            ],
            'user' => $user,
        ]);
    }
}

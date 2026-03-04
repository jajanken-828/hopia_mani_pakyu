<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
// Added for CRM dashboard stats
use App\Models\Client;
use App\Models\Product; // Added for CRM integration
use App\Models\PurchaseOrder; // Added for HRM audit logs
use App\Models\User; // Added for ECO dashboard stats
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
                    'progress' => 45, // Example static progress
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
            // Fetch trainees suggested by HRM Staff for promotion
            $suggestedTrainees = User::where('promotion_suggested', true)
                ->orderBy('suggested_at', 'desc')
                ->get();

            return Inertia::render('Dashboard/HRM/Manager/Index', [
                'suggestedTrainees' => $suggestedTrainees,
                'stats' => [
                    'totalEmployees' => User::count(),
                    'activeRecruitment' => 12,
                    'pendingLeaves' => 8,
                    'attendanceRate' => 95,
                ],
            ]);
        }

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
                // Full URL for the profile photo
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
            'employees' => $employees, // Use the mapped variable here
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

        // Save the action to your new AuditLog table
        \App\Models\AuditLog::create([
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
     * This moves them from 'trainee' to 'staff' and clears the suggestion flag.
     */
    public function finalizePromotion($id)
    {
        $trainee = User::findOrFail($id);

        $trainee->update([
            'position' => 'staff',
            'promotion_suggested' => false,
            'suggested_at' => null,
        ]);

        return redirect()->back()->with('message', "{$trainee->name} has been successfully promoted to Staff.");
    }

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
                'inventoryItems' => [],
                'pendingOrders' => [],
            ]);
        }

        return Inertia::render('Dashboard/SCM/Employee/Index', [
            'stats' => [
                'pendingPickups' => 5,
                'incomingShipments' => 3,
                'inventoryChecks' => 2,
            ],
            'user' => Auth::user(),
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
        if ($position === 'manager') {
            return Inertia::render('Dashboard/INV/Manager/index', [
                'user' => Auth::user(),
                'stockLevels' => [
                    ['id' => 1, 'name' => 'Cotton Fabric', 'sku' => 'CF-001', 'quantity' => 150, 'status' => 'In Stock'],
                    ['id' => 2, 'name' => 'Polyester Blend', 'sku' => 'PB-002', 'quantity' => 85, 'status' => 'In Stock'],
                    ['id' => 3, 'name' => 'Silk Material', 'sku' => 'SM-003', 'quantity' => 25, 'status' => 'Low Stock'],
                    ['id' => 4, 'name' => 'Denim', 'sku' => 'DN-004', 'quantity' => 200, 'status' => 'In Stock'],
                    ['id' => 5, 'name' => 'Wool Blend', 'sku' => 'WB-005', 'quantity' => 8, 'status' => 'Critical'],
                ],
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
            'stockLevels' => [
                ['id' => 1, 'name' => 'Cotton Fabric', 'sku' => 'CF-001', 'quantity' => 150, 'status' => 'In Stock'],
                ['id' => 2, 'name' => 'Polyester Blend', 'sku' => 'PB-002', 'quantity' => 85, 'status' => 'In Stock'],
                ['id' => 3, 'name' => 'Silk Material', 'sku' => 'SM-003', 'quantity' => 25, 'status' => 'Low Stock'],
                ['id' => 4, 'name' => 'Denim', 'sku' => 'DN-004', 'quantity' => 200, 'status' => 'In Stock'],
                ['id' => 5, 'name' => 'Wool Blend', 'sku' => 'WB-005', 'quantity' => 8, 'status' => 'Critical'],
                ['id' => 6, 'name' => 'Linen', 'sku' => 'LN-006', 'quantity' => 45, 'status' => 'In Stock'],
                ['id' => 7, 'name' => 'Satin', 'sku' => 'ST-007', 'quantity' => 60, 'status' => 'In Stock'],
                ['id' => 8, 'name' => 'Velvet', 'sku' => 'VL-008', 'quantity' => 15, 'status' => 'Low Stock'],
            ],
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
                    'pendingApprovals' => \App\Models\PurchaseOrder::whereIn('status', ['credit_review', 'tier_assignment'])
                        ->count(),
                    'conversionRate' => (int) $conversionRate,
                ],
                'dailyActivityCount' => \App\Models\CrmInteraction::whereDate('created_at', \Carbon\Carbon::today())->count(),
                'leaderboard' => \App\Models\User::where('role', 'CRM')
                    ->where('position', 'staff')
                    ->withCount(['leads as won_deals' => function ($query) {
                        $query->where('status', 'Closed-Won');
                    }])
                    ->orderBy('won_deals', 'desc')
                    ->get()
                    ->map(fn ($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'won_deals' => $user->won_deals,
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

        // 1. Fetch Real Product Data (Crucial for the Ledger)
        // We use paginate(10) so that 'products.data' is available in Vue
        $products = Product::orderBy('name', 'asc')->paginate(10);

        $pendingCompanies = Client::whereIn('status', ['pending', 'Pending'])->latest()->get();
        $verifiedCompanies = Client::whereNotIn('status', ['pending', 'Pending'])->latest()->get();

        // 2. Calculate Real-time Pipeline counts
        $pendingCreditCount = PurchaseOrder::where('status', 'credit_review')->count();
        $pendingTieringCount = PurchaseOrder::where('status', 'tier_assignment')->count();

        // 3. Revenue Metrics
        $todaySales = PurchaseOrder::where('status', 'approved')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthlyRevenue = PurchaseOrder::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        $pipelineDetails = PurchaseOrder::with('client')
            ->whereIn('status', ['credit_review', 'tier_assignment'])
            ->latest()
            ->get();

        return Inertia::render($view, [
            'user' => Auth::user(),
            'products' => $products, // This populates the Inventory Ledger
            'pendingCompanies' => $pendingCompanies,
            'verifiedCompanies' => $verifiedCompanies,
            'onlineSales' => PurchaseOrder::with('client')->latest()->take(5)->get(),
            'pipelineDetails' => $pipelineDetails,
            'stats' => [
                'todaySales' => number_format($todaySales, 2),
                'monthlyRevenue' => number_format($monthlyRevenue, 2),
                'activeProducts' => Product::where('status', 'published')->count(),
                'lowStockCount' => Product::where('stock', '<', 50)->count(),
                'pendingCredit' => $pendingCreditCount, // For Department Pipeline
                'pendingTiering' => $pendingTieringCount, // For Department Pipeline
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

<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\Auth\SupplierAuthController;
use App\Http\Controllers\client\DashboardController as ClientDashboardController;
use App\Http\Controllers\client\InvoicesController;
use App\Http\Controllers\client\OrdersController;
use App\Http\Controllers\client\SupportController;
use App\Http\Controllers\crm\manager\ApprovalController;
use App\Http\Controllers\crm\manager\OversightController;
use App\Http\Controllers\crm\manager\StrategyController;
use App\Http\Controllers\crm\staff\CustomerprofileController;
use App\Http\Controllers\crm\staff\LeadController;
use App\Http\Controllers\crm\staff\StaffDayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\eco\manager\BookController;
use App\Http\Controllers\eco\manager\ClientVerificationController;
use App\Http\Controllers\eco\manager\CreditController;
use App\Http\Controllers\eco\staff\CustomerController;
use App\Http\Controllers\eco\staff\OrdermngController;
use App\Http\Controllers\eco\staff\ProductsController;
use App\Http\Controllers\hrm\employee\AttendanceController;
use App\Http\Controllers\hrm\employee\HolidayController;
use App\Http\Controllers\hrm\employee\HrmstaffpayrollController;
use App\Http\Controllers\hrm\employee\InterviewController;
use App\Http\Controllers\hrm\employee\LeaveController;
use App\Http\Controllers\hrm\employee\TrainingController;
use App\Http\Controllers\hrm\manager\AnalyticsController;
use App\Http\Controllers\hrm\manager\ApplicantController;
use App\Http\Controllers\hrm\manager\OnboardingController;
use App\Http\Controllers\hrm\manager\PayrollController;
use App\Http\Controllers\inv\InventoryController as InvInventoryController;
use App\Http\Controllers\inv\MaterialController;
use App\Http\Controllers\inv\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\scm\employee\InboundController;
use App\Http\Controllers\scm\employee\InventoryController;
use App\Http\Controllers\scm\employee\RecievingController;
use App\Http\Controllers\scm\employee\VerificationController;
use App\Http\Controllers\scm\manager\AuditController;
use App\Http\Controllers\scm\manager\CloseController;
use App\Http\Controllers\scm\manager\ProcurementController;
use App\Http\Controllers\SUPPLIERS\SupplierDashboardController;
use App\Http\Controllers\trainee\TraineeAttendanceController;
use App\Http\Controllers\trainee\TraineePayslipController;
use App\Http\Controllers\trainee\TraineeTimeKeepingController;
use App\Http\Controllers\users\AppController;
use App\Http\Controllers\users\ClockController;
use App\Http\Controllers\users\leaveController as UserLeaveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return inertia('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/apply', function () {
    return inertia('Auth/apply');
})->name('apply');

// This route handles the actual form submission
Route::post('/apply/store', [ApplicantController::class, 'store'])->name('applicants.public.store');

Route::middleware(['auth'])->group(function () {
    // The main entry point that redirects based on user role/position
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // NEW: Specific route for the Employee Login UI (pointing to USERS/app.vue)
    // routes/web.php

});

/*
|--------------------------------------------------------------------------
| Employee Unified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'position:staff,manager'])->prefix('dashboard/employee-ui')->group(function () {

    // Your main app.vue page
    Route::get('/', [AppController::class, 'index'])->name('employee.ui.dashboard');

    // New Page: Schedule
    // Updated to use the Controller for the GET request
    Route::get('/clock', [ClockController::class, 'clock'])->name('employee.ui.clock');

    // NEW: The POST route to handle the actual button click
    Route::post('/clock/toggle', [ClockController::class, 'toggle'])->name('employee.attendance.toggle');

    // New Page: Tasks
    // GET: Display the leave page and fetch history from the controller
    Route::get('/leave', [UserLeaveController::class, 'leave'])->name('employee.ui.leave');

    // POST: Submit the form and save to database
    Route::post('/leave', [UserLeaveController::class, 'store'])->name('employee.leave.store');

    Route::get('/payslip', function () {
        return inertia('Dashboard/USERS/payslip');
    })->name('employee.ui.payslip');

});

/*
|--------------------------------------------------------------------------
| Trainee Unified Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/trainee')->middleware(['auth', 'verified', 'position:trainee'])->group(function () {
    // Unified dashboard for all trainees regardless of role (HRM, SCM, etc.)
    Route::get('/', [DashboardController::class, 'index'])->name('trainee.dashboard');

    // Time Keeping / Clock In/Out
    Route::get('/timekeeping', [TraineeTimeKeepingController::class, 'index'])->name('trainee.timekeeping');
    Route::post('/timekeeping/clock', [TraineeTimeKeepingController::class, 'clockInOut'])->name('trainee.timekeeping.clock');

    // Attendance Records
    Route::get('/attendance', [TraineeAttendanceController::class, 'index'])->name('trainee.attendance');

    // Payslips
    Route::get('/payslip', [TraineePayslipController::class, 'index'])->name('trainee.payslip');
    Route::get('/payslip/{payroll}', [TraineePayslipController::class, 'show'])->name('trainee.payslip.show');
});

/*
|--------------------------------------------------------------------------
| HRM Department Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/hrm')->name('hrm.')->middleware(['auth', 'verified'])->group(function () {

    // Pointing to consolidated index logic for HRM Staff
    Route::get('/employee', [DashboardController::class, 'index'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.dashboard');

    Route::post('/employee/{id}/update', [DashboardController::class, 'updateEmployee'])->name('employees.update');
    Route::delete('/employee/{id}', [DashboardController::class, 'destroy'])->name('employees.destroy');

    Route::get('/training', [TrainingController::class, 'training'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.training');

    // New Grading Route
    Route::post('/training/grade/{id}', [TrainingController::class, 'submitGrade'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('training.grade');

    /**
     * PROMOTION SUGGESTION ROUTE (Staff Side)
     * This connects the Training.vue modal to TrainingController@suggestPromotion
     */
    Route::post('/training/suggest-promotion/{id}', [TrainingController::class, 'suggestPromotion'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('training.suggest-promotion');

    Route::get('/leave', [LeaveController::class, 'leave'])
        ->middleware(['role:HRM', 'position:staff,manager'])
        ->name('employee.leave');

    // Route for Approve/Reject buttons (PATCH)
    Route::patch('/leave/{leaveRequest}', [LeaveController::class, 'update'])->name('employee.leave.update');

    // Route for the Manual Entry form (POST)
    Route::post('/leave/manual', [LeaveController::class, 'store_manual'])->name('employee.leave.store_manual');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->middleware(['role:HRM', 'position:staff,manager'])
        ->name('employee.attendance');

    Route::post('/attendance/toggle', [AttendanceController::class, 'toggle'])
        ->name('employee.attendance.toggle');

    // Soft-delete a single shift for a specific employee on a specific date.
    // Vue always calls this via router.delete() — keep only the DELETE route to avoid name collision.
    Route::delete('/attendance/shift', [AttendanceController::class, 'destroyShift'])
        ->name('employee.attendance.destroy-shift');

    // Soft-delete all shifts for an employee across a whole month.
    Route::post('/attendance/shift/remove-monthly', [AttendanceController::class, 'destroyMonthlyShift'])
        ->name('employee.attendance.destroy-monthly-shift');

    // Approval routes — names must NOT repeat 'hrm.' since the parent group already adds it
    Route::post('/attendance/approve-shift', [AttendanceController::class, 'approveShift'])->name('employee.attendance.approve-shift');
    Route::post('/attendance/reject-shift', [AttendanceController::class, 'rejectShift'])->name('employee.attendance.reject-shift');
    Route::post('/attendance/approve-holiday', [AttendanceController::class, 'approveHoliday'])->name('employee.attendance.approve-holiday');
    Route::post('/attendance/reject-holiday', [AttendanceController::class, 'rejectHoliday'])->name('employee.attendance.reject-holiday');

    // No position middleware here — both staff and managers use this; controller checks internally
    Route::post('/attendance/update-shift', [AttendanceController::class, 'updateShift'])
        ->name('employee.attendance.update-shift');

    Route::prefix('holidays')->name('employee.holidays.')->group(function () {
        Route::post('/', [HolidayController::class, 'store'])->name('store');
        Route::patch('/{holiday}', [HolidayController::class, 'update'])->name('update');
        Route::delete('/{holiday}', [HolidayController::class, 'destroy'])->name('destroy');
    });

    Route::get('/interview', [InterviewController::class, 'interview'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview');

    Route::post('/InterviewController/update-status', [InterviewController::class, 'submitStatus'])
        ->middleware(['role:HRM', 'position:staff']);

    // Inside the hrm prefix group...
    Route::post('/interview/{interview}/evaluate', [InterviewController::class, 'updateStatus'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview.evaluate');

    // Inside your hrm prefix and employee/staff middleware group:
    Route::post('/interview/{interview}/reschedule', [InterviewController::class, 'reschedule'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview.reschedule');

    // --- HRM Manager Routes ---
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/hrmstaffpayroll', [HrmstaffpayrollController::class, 'hrmstaffpayroll'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.hrmstaffpayroll');

    Route::post('/payroll/store', [HrmstaffpayrollController::class, 'store'])
        ->name('hrm.employee.payroll.store');

    Route::patch('/payroll/{id}/approve', [HrmstaffpayrollController::class, 'approve'])
        ->name('employee.payroll.approve');

    Route::patch('/payroll/{id}/reject', [HrmstaffpayrollController::class, 'reject'])
        ->name('employee.payroll.reject');

    Route::post('/holidays/store', [HrmstaffpayrollController::class, 'storeHoliday'])
        ->name('hrm.holidays.store');

    /**
     * FINALIZE PROMOTION ROUTE (Manager Side)
     * This allows the Manager to approve the suggestion and upgrade Trainee to Staff.
     * NOTE: No extra role/position middleware here — the parent group already enforces
     * auth + verified, and the controller re-checks via Auth::user() as needed.
     * Extra middleware on POST routes can silently redirect (302) in Inertia,
     * causing onSuccess to fire without the DB write ever happening.
     */
    Route::post('/manager/finalize-promotion/{id}', [DashboardController::class, 'finalizePromotion'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.finalize-promotion');

    /**
     * GRADE & AUTO-PROMOTE ROUTE (Manager Side)
     * Manager grades a trainee directly across 5 criteria (1-5 stars each).
     * If total percentage >= 80%, the trainee is automatically promoted to Staff.
     * NOTE: Middleware removed for the same reason as finalize-promotion above.
     */
    Route::post('/manager/grade-trainee/{id}', [DashboardController::class, 'gradeAndPromote'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.grade-trainee');

    // Updated Applicant Routes
    Route::controller(ApplicantController::class)->group(function () {
        Route::get('/applicants', 'index')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.index');

        Route::post('/applicants', 'store')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.store');

        Route::post('/applicants/{applicant}/schedule', 'scheduleInterview')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.schedule');

        Route::patch('/applicants/{applicant}/update-stage', 'updateStage')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.update-stage');

        Route::post('/applicants/{applicant}/create-user', 'createUser')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.create-user');
    });

    // --- Onboarding & Pipeline Routes ---
    Route::controller(OnboardingController::class)->group(function () {
        Route::get('/onboarding', 'onboarding')
            ->middleware(['role:HRM', 'position:manager'])
            ->name('manager.onboarding');
    });

    Route::get('/payroll', [PayrollController::class, 'payroll'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.payroll');

    Route::get('/analytics', [AnalyticsController::class, 'analytics'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.analytics');
});

/*
|--------------------------------------------------------------------------
| SCM Department Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/scm')->name('scm.')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/procurement', [ProcurementController::class, 'procurement'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.procurement');

    Route::get('/audit', [AuditController::class, 'audit'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.audit');

    Route::get('/close', [CloseController::class, 'close'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.close');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.dashboard');

    Route::get('/inbound', [InboundController::class, 'inbound'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.inbound');

    Route::get('/inventory', [InventoryController::class, 'inventory'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.inventory');

    Route::get('/recieving', [RecievingController::class, 'recieving'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.recieving');

    Route::get('/verification', [VerificationController::class, 'verification'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.verification');
});

/*
|--------------------------------------------------------------------------
| Additional Department Dashboards
|--------------------------------------------------------------------------
*/

// Finance (FIN)
Route::prefix('dashboard/fin')->name('fin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:FIN', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:FIN', 'position:staff'])
        ->name('employee.dashboard');
});

// Manufacturing (MAN)
Route::prefix('dashboard/man')->name('man.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:MAN', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:MAN', 'position:staff'])
        ->name('employee.dashboard');
});

// Inventory (INV)
Route::prefix('dashboard/inv')->name('inv.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:INV', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/inventory', [InvInventoryController::class, 'inventory'])
        ->middleware(['role:INV', 'position:manager'])
        ->name('manager.inventory');

    Route::get('/product', [ProductController::class, 'product'])
        ->middleware(['role:INV', 'position:manager'])
        ->name('manager.product');

    Route::get('/material', [MaterialController::class, 'material'])
        ->middleware(['role:INV', 'position:manager'])
        ->name('manager.material');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:INV', 'position:staff'])
        ->name('employee.dashboard');
});

// Order Management (ORD)
Route::prefix('dashboard/ord')->name('ord.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:ORD', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:ORD', 'position:staff'])
        ->name('employee.dashboard');
});

// Warehouse (WAR)
Route::prefix('dashboard/war')->name('war.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:WAR', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:WAR', 'position:staff'])
        ->name('employee.dashboard');
});

// Inside the CRM group
Route::prefix('dashboard/crm')->name('crm.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/approval', [ApprovalController::class, 'approval'])->name('approval');
    Route::post('/approval/{id}/process', [ApprovalController::class, 'process'])->name('approval.process');
    Route::get('/oversight', [OversightController::class, 'oversight'])->name('oversight');
    Route::get('/strategy', [StrategyController::class, 'strategy'])->name('strategy');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:staff'])
        ->name('employee.dashboard');
    Route::get('/my-day', [StaffDayController::class, 'index'])->name('staff.day');
    Route::get('/lead', [LeadController::class, 'lead'])->name('lead');
    Route::post('/lead/store', [LeadController::class, 'store'])->name('lead.store');
    Route::patch('/lead/{id}/status', [LeadController::class, 'updateStatus'])->name('lead.status');

    Route::post('/lead/convert', [LeadController::class, 'convertToClient'])->name('lead.convert');

    // Customer profile – optional ID
    Route::get('/customerprofile/{id?}', [CustomerprofileController::class, 'customerprofile'])
        ->name('customerprofile');

    // Store interaction (one definition only)
    Route::post('/interaction/store', [CustomerprofileController::class, 'storeInteraction'])
        ->name('interaction.store');
});

// E-Commerce (ECO)
Route::prefix('dashboard/eco')->name('eco.')->middleware(['auth', 'verified'])->group(function () {

    // Manager Routes
    Route::middleware(['role:ECO', 'position:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/book', [BookController::class, 'book'])->name('book');
        Route::post('/book/store', [BookController::class, 'store'])->name('book.store');
        Route::patch('/book/{tier}/update', [BookController::class, 'update'])->name('book.update');
        Route::post('/book/apply-tier/{po}', [BookController::class, 'applyTier'])->name('book.apply-tier');
        Route::get('/credit', [CreditController::class, 'credit'])->name('credit');
        Route::post('/credit', [CreditController::class, 'store'])->name('credit.store');
        Route::post('/credit/verify/{po}', [CreditController::class, 'verifyOrder'])->name('credit.verify');
        Route::patch('/credit/{account}/toggle', [CreditController::class, 'toggleStatus'])->name('credit.toggle-status');
        Route::delete('/credit/{account}', [CreditController::class, 'destroy'])->name('credit.destroy');
        Route::get('/verification', [ClientVerificationController::class, 'index'])->name('verification.index');

        Route::patch('/clients/{client}/status', [ClientVerificationController::class, 'updateStatus'])->name('clients.status.update');
    });

    // Staff / Employee Routes
    Route::middleware(['role:ECO', 'position:staff'])->group(function () {
        Route::get('/staff', [DashboardController::class, 'index'])->name('employee.dashboard');

        // This resolves the "Target class does not exist" error
        Route::get('/products', [ProductsController::class, 'products'])->name('employee.products');
        Route::post('/products', [ProductsController::class, 'store'])->name('employee.products.store');

        Route::post('/products/{product}/update', [ProductsController::class, 'update'])->name('employee.products.update');
        Route::patch('/products/{product}/toggle', [ProductsController::class, 'toggleStatus'])->name('employee.products.toggle');

        Route::get('/ordermng', [OrdermngController::class, 'ordermng'])->name('employee.ordermng');
        Route::get('/customer', [CustomerController::class, 'customer'])->name('employee.customer');
    });
});

/*
|--------------------------------------------------------------------------
| B2B Client Authentication Routes (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:client')->group(function () {
    Route::get('client/register', [ClientAuthController::class, 'create'])->name('client.register');
    Route::post('client/register', [ClientAuthController::class, 'store'])->name('client.register.store');
    Route::get('client/login', [ClientAuthController::class, 'showLogin'])->name('client.login');
    Route::post('client/login', [ClientAuthController::class, 'login'])->name('client.login.store');
});

/*
|--------------------------------------------------------------------------
| Protected Client Routes (B2B PORTAL)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:client')->prefix('partner')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrdersController::class, 'orders'])->name('orders');
    Route::post('/purchase-order', [ClientDashboardController::class, 'placeOrder'])->name('purchase-order.store');
    Route::post('/quotation/{po}/accept', [ClientDashboardController::class, 'acceptQuotation'])->name('quotation.accept');
    Route::get('/invoices', [InvoicesController::class, 'invoices'])->name('invoices');
    Route::get('/support', [SupportController::class, 'support'])->name('support');
});

/*
|--------------------------------------------------------------------------
| Supplier Authentication Routes
|--------------------------------------------------------------------------
*/

// Supplier Public Routes (guests only)
Route::middleware('guest:supplier')->group(function () {
    Route::get('supplier/login', [SupplierAuthController::class, 'showLogin'])->name('supplier.login');
    Route::post('supplier/login', [SupplierAuthController::class, 'login'])->name('supplier.login.store');
    Route::get('supplier/register', [SupplierAuthController::class, 'create'])->name('supplier.register');
    Route::post('supplier/register', [SupplierAuthController::class, 'store'])->name('supplier.register.store');
});

/*
 * Supplier logout MUST live outside the guest:supplier block above.
 * A logged-in supplier is not a guest — putting logout inside guest middleware
 * would block it with a redirect, silently leaving the supplier session alive.
 * We protect it with auth:supplier instead so only authenticated suppliers can call it.
 */
Route::post('supplier/logout', [SupplierAuthController::class, 'logout'])
    ->middleware('auth:supplier')
    ->name('supplier.logout');

// Protected Supplier Routes
Route::middleware('auth:supplier')->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';

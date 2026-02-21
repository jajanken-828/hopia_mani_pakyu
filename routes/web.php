<?php

use App\Http\Controllers\Auth\ClientAuthController;
use App\Http\Controllers\client\DashboardController as ClientDashboardController;
use App\Http\Controllers\client\InvoicesController;
use App\Http\Controllers\client\OrdersController;
use App\Http\Controllers\client\SupportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\eco\manager\BookController;
use App\Http\Controllers\eco\manager\ClientVerificationController;
use App\Http\Controllers\eco\manager\CreditController;
use App\Http\Controllers\eco\staff\CustomerController;
use App\Http\Controllers\eco\staff\OrdermngController;
use App\Http\Controllers\eco\staff\ProductsController;
use App\Http\Controllers\hrm\employee\AttendanceController;
use App\Http\Controllers\hrm\employee\HrmstaffpayrollController;
use App\Http\Controllers\hrm\employee\InterviewController;
use App\Http\Controllers\hrm\employee\LeaveController;
use App\Http\Controllers\hrm\employee\TrainingController;
use App\Http\Controllers\hrm\manager\AnalyticsController;
use App\Http\Controllers\hrm\manager\ApplicantController;
use App\Http\Controllers\hrm\manager\OnboardingController;
use App\Http\Controllers\hrm\manager\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\scm\employee\InboundController;
use App\Http\Controllers\scm\employee\InventoryController;
use App\Http\Controllers\scm\employee\RecievingController;
use App\Http\Controllers\scm\employee\VerificationController;
use App\Http\Controllers\scm\manager\AuditController;
use App\Http\Controllers\scm\manager\CloseController;
use App\Http\Controllers\scm\manager\SourcingController;
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
    Route::get('/', function () {
        return inertia('Dashboard/USERS/app');
    })->name('employee.ui.dashboard');

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
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.leave');

    // Route for Approve/Reject buttons (PATCH)
    Route::patch('/leave/{leaveRequest}', [LeaveController::class, 'update'])->name('employee.leave.update');

    // Route for the Manual Entry form (POST)
    Route::post('/leave/manual', [LeaveController::class, 'store_manual'])->name('employee.leave.store_manual');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.attendance');

    // Inside web.php
    Route::post('/attendance/toggle', [App\Http\Controllers\hrm\employee\AttendanceController::class, 'toggle'])
        ->name('employee.attendance.toggle');

    Route::post('/attendance/update-shift', [AttendanceController::class, 'updateShift'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.attendance.update-shift');

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

    Route::post('/payroll/store', [hrmstaffpayrollController::class, 'store'])
        ->name('hrm.employee.payroll.store');

    // routes/web.php inside the hrm. prefix group

    Route::patch('/payroll/{id}/approve', [HrmstaffpayrollController::class, 'approve'])
        ->name('employee.payroll.approve');

    Route::patch('/payroll/{id}/reject', [HrmstaffpayrollController::class, 'reject'])
        ->name('employee.payroll.reject');

    Route::post('/holidays/store', [hrmstaffpayrollController::class, 'storeHoliday'])
        ->name('hrm.holidays.store');

    /**
     * FINALIZE PROMOTION ROUTE (Manager Side)
     * This allows the Manager to approve the suggestion and upgrade Trainee to Staff
     */
    Route::post('/manager/finalize-promotion/{id}', [DashboardController::class, 'finalizePromotion'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.finalize-promotion');

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

    Route::get('/sourcing', [SourcingController::class, 'sourcing'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.sourcing');

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

// CRM
Route::prefix('dashboard/crm')->name('crm.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:staff'])
        ->name('employee.dashboard');
});

// E-Commerce (ECO)
Route::prefix('dashboard/eco')->name('eco.')->middleware(['auth', 'verified'])->group(function () {

    // Manager Routes
    Route::middleware(['role:ECO', 'position:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/book', [BookController::class, 'book'])->name('book');
        Route::get('/credit', [CreditController::class, 'credit'])->name('credit');
        Route::get('/verification', [ClientVerificationController::class, 'index'])->name('verification.index');
        Route::patch('/clients/{client}/status', [ClientVerificationController::class, 'updateStatus'])->name('clients.status.update');
    });

    // Staff / Employee Routes
    Route::middleware(['role:ECO', 'position:staff'])->group(function () {
        Route::get('/staff', [ClientDashboardController::class, 'index'])->name('employee.dashboard');

        // This resolves the "Target class does not exist" error
        Route::get('/products', [ProductsController::class, 'products'])->name('employee.products');
        Route::post('/products', [ProductsController::class, 'store'])->name('employee.products.store');
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
| Protected Client Routes (B2B PORTAL) – FIXED REDIRECT
|--------------------------------------------------------------------------
*/
// Protected Client Routes (B2B PORTAL)
Route::middleware('auth:client')->prefix('partner')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    // Ensure this matches the Controller and the View folder
    Route::get('/orders', [OrdersController::class, 'orders'])->name('orders');
    Route::get('/invoices', [InvoicesController::class, 'invoices'])->name('invoices');
    Route::get('/support', [SupportController::class, 'support'])->name('support');
});

require __DIR__.'/auth.php';

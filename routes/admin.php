<?php

use App\Http\Controllers\Admin\Ai\AiController;
use App\Http\Controllers\Admin\Announcement\AnnouncementController;
use App\Http\Controllers\Admin\Attendance\AttendanceController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Document\DocumentController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Expense\ExpenseController;
use App\Http\Controllers\Admin\Export\ExportController;
use App\Http\Controllers\Admin\Holiday\HolidayController;
use App\Http\Controllers\Admin\Leave\LeaveController;
use App\Http\Controllers\Admin\Notification\NotificationController;
use App\Http\Controllers\Admin\Payroll\PayrollController;
use App\Http\Controllers\Admin\Performance\PerformanceController;
use App\Http\Controllers\Admin\Report\ReportController;
use App\Http\Controllers\Admin\RolePermission\RolePermissionController;
use App\Http\Controllers\Admin\Setting\SettingController;
use App\Http\Controllers\Admin\Shift\ShiftController;
use App\Http\Controllers\Portal\PortalAttendanceController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Portal\PortalExpenseController;
use App\Http\Controllers\Portal\PortalLeaveController;
use App\Http\Controllers\Portal\PortalPayrollController;
use App\Http\Controllers\Portal\PortalPerformanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Employee Self-Service (ESS) Portal Routes
Route::prefix('portal')->name('portal::')->middleware('auth')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('dashboard', [PortalController::class, 'dashboard'])->name('dashboard.index');

    // Attendance
    Route::get('attendance', [PortalAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/check-in', [PortalAttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::post('attendance/check-out', [PortalAttendanceController::class, 'checkOut'])->name('attendance.checkOut');

    // Leaves
    Route::get('leaves', [PortalLeaveController::class, 'index'])->name('leaves.index');
    Route::post('leaves', [PortalLeaveController::class, 'store'])->name('leaves.store');

    // Expenses
    Route::get('expenses', [PortalExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses', [PortalExpenseController::class, 'store'])->name('expenses.store');

    // Payroll
    Route::get('payroll', [PortalPayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/{id}/payslip', [PortalPayrollController::class, 'payslip'])->name('payroll.payslip');

    // Performance
    Route::get('performance', [PortalPerformanceController::class, 'index'])->name('performance.index');
    Route::patch('performance/{id}/progress', [PortalPerformanceController::class, 'updateProgress'])->name('performance.updateProgress');
});

// Admin Panel Routes
Route::prefix('admin')->name('admin::')->group(function () {
    // Guest Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('login', function () {
            return view('admin.auth.login');
        })->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('authenticate');
    });

    // Authenticated Admin Routes
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // 1. Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.admin');

        // 2. Workforce - Employees & Documents
        Route::resource('employees', EmployeeController::class);
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::patch('{id}/verify', [DocumentController::class, 'verify'])->name('verify');
            Route::delete('{id}', [DocumentController::class, 'destroy'])->name('destroy');
        });

        // 3. Workforce - Attendance & Shifts
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('checkIn');
            Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('checkOut');
        });
        Route::prefix('shifts')->name('shifts.')->group(function () {
            Route::get('/', [ShiftController::class, 'index'])->name('index');
            Route::post('/', [ShiftController::class, 'store'])->name('store');
            Route::put('{id}', [ShiftController::class, 'update'])->name('update');
            Route::delete('{id}', [ShiftController::class, 'destroy'])->name('destroy');
            Route::post('assign', [ShiftController::class, 'assign'])->name('assign');
        });

        // 4. Workforce - Leaves & Holidays
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('requests', [LeaveController::class, 'requests'])->name('requests');
            Route::get('index', [LeaveController::class, 'requests'])->name('index');
            Route::post('requests', [LeaveController::class, 'store'])->name('store');
            Route::patch('requests/{id}/status', [LeaveController::class, 'process'])->name('process');
            Route::get('types', [LeaveController::class, 'types'])->name('types');
        });
        Route::resource('holidays', HolidayController::class)->except(['create', 'show', 'edit']);

        // 5. Finance & Payroll
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::post('generate', [PayrollController::class, 'generate'])->name('generate');
            Route::patch('{id}/pay', [PayrollController::class, 'markAsPaid'])->name('pay');
            Route::get('{id}/payslip', [PayrollController::class, 'payslip'])->name('payslip');
            Route::get('structures', [PayrollController::class, 'structures'])->name('structures');
            Route::post('structures', [PayrollController::class, 'updateStructure'])->name('structures.update');
        });
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::patch('{id}/status', [ExpenseController::class, 'process'])->name('process');
        });

        // 6. Performance & Talent
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('goals', [PerformanceController::class, 'goals'])->name('goals');
            Route::post('goals', [PerformanceController::class, 'storeGoal'])->name('goals.store');
            Route::patch('goals/{id}/progress', [PerformanceController::class, 'updateGoalProgress'])->name('goals.progress');
            Route::delete('goals/{id}', [PerformanceController::class, 'destroyGoal'])->name('goals.destroy');
            Route::get('reviews', [PerformanceController::class, 'reviews'])->name('reviews');
            Route::post('reviews', [PerformanceController::class, 'storeReview'])->name('reviews.store');
        });

        // 7. Communication & AI Suite
        Route::resource('announcements', AnnouncementController::class)->except(['create', 'show', 'edit']);
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/', [AiController::class, 'index'])->name('index');
            Route::post('chat', [AiController::class, 'chat'])->name('chat');
            Route::post('craft-announcement', [AiController::class, 'craftAnnouncement'])->name('craftAnnouncement');
            Route::post('generate-review', [AiController::class, 'generateReview'])->name('generateReview');
            Route::post('scan-receipt', [AiController::class, 'scanReceipt'])->name('scanReceipt');
            Route::post('parse-resume', [AiController::class, 'parseResume'])->name('parseResume');
        });
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::patch('mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
            Route::post('send-test', [NotificationController::class, 'sendTest'])->name('sendTest');
        });

        // 8. Administration & Settings
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('headcount', [ReportController::class, 'headcount'])->name('headcount');
            Route::get('attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('leaves', [ReportController::class, 'leaves'])->name('leaves');
            Route::get('payroll', [ReportController::class, 'payroll'])->name('payroll');
        });
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::post('/', [RolePermissionController::class, 'store'])->name('store');
            Route::put('{id}', [RolePermissionController::class, 'update'])->name('update');
            Route::delete('{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
            Route::get('permissions', [RolePermissionController::class, 'permissions'])->name('permissions');
            Route::post('assign-user', [RolePermissionController::class, 'assignUser'])->name('assignUser');
        });
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('departments', [SettingController::class, 'departments'])->name('departments');
            Route::post('departments', [SettingController::class, 'storeDepartment'])->name('departments.store');
            Route::put('departments/{id}', [SettingController::class, 'updateDepartment'])->name('departments.update');
            Route::delete('departments/{id}', [SettingController::class, 'destroyDepartment'])->name('departments.destroy');

            Route::get('designations', [SettingController::class, 'designations'])->name('designations');
            Route::post('designations', [SettingController::class, 'storeDesignation'])->name('designations.store');
            Route::put('designations/{id}', [SettingController::class, 'updateDesignation'])->name('designations.update');
            Route::delete('designations/{id}', [SettingController::class, 'destroyDesignation'])->name('designations.destroy');

            Route::get('company', [SettingController::class, 'company'])->name('company');
            Route::put('company', [SettingController::class, 'updateCompany'])->name('company.update');
        });

        // 9. Export & Report Generators
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('employees/csv', [ExportController::class, 'employeesCsv'])->name('employees.csv');
            Route::get('employees/print', [ExportController::class, 'employeesPrint'])->name('employees.print');
            Route::get('attendance/csv', [ExportController::class, 'attendanceCsv'])->name('attendance.csv');
            Route::get('payroll/csv', [ExportController::class, 'payrollCsv'])->name('payroll.csv');
            Route::get('payroll/print', [ExportController::class, 'payrollPrint'])->name('payroll.print');
            Route::get('leaves/csv', [ExportController::class, 'leavesCsv'])->name('leaves.csv');
            Route::get('expenses/csv', [ExportController::class, 'expensesCsv'])->name('expenses.csv');
        });
    });
});

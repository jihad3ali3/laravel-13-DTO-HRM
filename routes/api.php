<?php

use App\Http\Controllers\Api\V1\Ai\AiController;
use App\Http\Controllers\Api\V1\Announcement\AnnouncementController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\DashboardController;
use App\Http\Controllers\Api\V1\Document\DocumentController;
use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use App\Http\Controllers\Api\V1\Expense\ExpenseController;
use App\Http\Controllers\Api\V1\Holiday\HolidayController;
use App\Http\Controllers\Api\V1\Leave\LeaveController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Payroll\PayrollController;
use App\Http\Controllers\Api\V1\Performance\PerformanceController;
use App\Http\Controllers\Api\V1\Report\ReportController;
use App\Http\Controllers\Api\V1\RolePermission\RolePermissionController;
use App\Http\Controllers\Api\V1\Setting\SettingController;
use App\Http\Controllers\Api\V1\Shift\ShiftController;

Route::prefix('v1')->name('api::')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('auth/profile', [AuthController::class, 'profile'])->name('profile');

        // 1. Attendance
        Route::prefix('attendance')->name('attendance.')->group(function (): void {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
            Route::get('daily-summary', [AttendanceController::class, 'dailySummary'])->name('daily-summary');
            Route::get('month-summary/{employeeId}', [AttendanceController::class, 'monthlySummary'])->name('month-summary');
        });

        // 2. Leaves Types, Application, & Balances
        Route::prefix('leaves')->name('leaves.')->group(function (): void {
            Route::get('types', [LeaveController::class, 'types'])->name('types');
            Route::get('requests', [LeaveController::class, 'requests'])->name('requests');
            Route::post('requests', [LeaveController::class, 'store'])->name('store');
            Route::get('balance/{employeeId}', [LeaveController::class, 'balances'])->name('balances');
            Route::patch('requests/{id}/status', [LeaveController::class, 'process'])->name('process')->middleware('permission:leaves.approve');
        });

        // 3. Holidays Calendar (Read-only for employees)
        Route::prefix('holidays')->name('holidays.')->group(function (): void {
            Route::get('/', [HolidayController::class, 'index'])->name('index');
            Route::get('{id}', [HolidayController::class, 'show'])->name('show');
            Route::middleware('permission:announcements.manage')->group(function (): void {
                Route::post('/', [HolidayController::class, 'store'])->name('store');
                Route::put('{id}', [HolidayController::class, 'update'])->name('update');
                Route::delete('{id}', [HolidayController::class, 'destroy'])->name('destroy');
            });
        });

        // 4. Expense Claims Submission & View details
        Route::prefix('expenses')->name('expenses.')->group(function (): void {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('{id}', [ExpenseController::class, 'show'])->name('show');
            Route::patch('{id}/status', [ExpenseController::class, 'process'])->name('process')->middleware('permission:expenses.approve');
        });

        // 5. Performance Goals & Reviews
        Route::prefix('performance')->name('performance.')->group(function (): void {
            Route::get('goals/{employeeId}', [PerformanceController::class, 'goals'])->name('goals');
            Route::patch('goals/{id}/progress', [PerformanceController::class, 'updateGoalProgress'])->name('goals.progress');
            Route::get('reviews', [PerformanceController::class, 'reviews'])->name('reviews');
            Route::get('reviews/{id}', [PerformanceController::class, 'showReview'])->name('reviews.show');
            Route::middleware('permission:performance.manage')->group(function (): void {
                Route::post('goals', [PerformanceController::class, 'storeGoal'])->name('goals.store');
                Route::delete('goals/{id}', [PerformanceController::class, 'destroyGoal'])->name('goals.destroy');
                Route::post('reviews', [PerformanceController::class, 'storeReview'])->name('reviews.store');
            });
        });

        // 6. Announcements Notice Board (Read-only for employees)
        Route::prefix('announcements')->name('announcements.')->group(function (): void {
            Route::get('/', [AnnouncementController::class, 'index'])->name('index');
            Route::get('{id}', [AnnouncementController::class, 'show'])->name('show');
            Route::middleware('permission:announcements.manage')->group(function (): void {
                Route::post('/', [AnnouncementController::class, 'store'])->name('store');
                Route::put('{id}', [AnnouncementController::class, 'update'])->name('update');
                Route::delete('{id}', [AnnouncementController::class, 'destroy'])->name('destroy');
            });
        });

        // 7. Employee Documents upload, view, & delete
        Route::prefix('documents')->name('documents.')->group(function (): void {
            Route::get('employee/{employeeId}', [DocumentController::class, 'index'])->name('employee.index');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::get('{id}', [DocumentController::class, 'show'])->name('show');
            Route::delete('{id}', [DocumentController::class, 'destroy'])->name('destroy');
            Route::patch('{id}/verify', [DocumentController::class, 'verify'])->name('verify')->middleware('permission:employees.edit');
        });

        // 8. Shifts (Read-only self shifts)
        Route::prefix('shifts')->name('shifts.')->group(function (): void {
            Route::get('employee/{employeeId}', [ShiftController::class, 'employeeShift'])->name('employee');
            Route::get('/', [ShiftController::class, 'index'])->name('index')->middleware('permission:shifts.view');
            Route::get('{id}', [ShiftController::class, 'show'])->name('show')->middleware('permission:shifts.view');
            Route::middleware('permission:shifts.manage')->group(function (): void {
                Route::post('/', [ShiftController::class, 'store'])->name('store');
                Route::put('{id}', [ShiftController::class, 'update'])->name('update');
                Route::delete('{id}', [ShiftController::class, 'destroy'])->name('destroy');
                Route::post('assign', [ShiftController::class, 'assign'])->name('assign');
            });
        });

        // 9. Notifications Hub & Channels management
        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('channels', [NotificationController::class, 'channels'])->name('channels');
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::patch('mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
            Route::patch('{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('send-test', [NotificationController::class, 'sendTest'])->name('send-test')->middleware('permission:settings.manage');
        });

        // 10. AI Enterprise HR Suite
        Route::prefix('ai')->name('ai.')->group(function (): void {
            Route::post('chat', [AiController::class, 'chat'])->name('chat');
            Route::post('scan-receipt', [AiController::class, 'scanReceipt'])->name('scan-receipt');
            Route::post('generate-review', [AiController::class, 'generateReview'])->name('generate-review');
            Route::post('parse-resume', [AiController::class, 'parseResume'])->name('parse-resume');
            Route::post('craft-announcement', [AiController::class, 'craftAnnouncement'])->name('craft-announcement');
        });

        // 11. Dashboard Metrics
        Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
            Route::get('metrics', [DashboardController::class, 'metrics'])->name('metrics')->middleware('permission:reports.view');
        });

        // 12. Employees Directory (CRUD)
        Route::prefix('employees')->name('employees.')->group(function (): void {
            Route::get('/', [EmployeeController::class, 'index'])->name('index')->middleware('permission:employees.edit');
            Route::post('/', [EmployeeController::class, 'store'])->name('store')->middleware('permission:employees.create');
            Route::get('{employee}', [EmployeeController::class, 'show'])->name('show')->middleware('permission:employees.edit');
            Route::put('{employee}', [EmployeeController::class, 'update'])->name('update')->middleware('permission:employees.edit');
            Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('destroy')->middleware('permission:employees.delete');
        });

        // 13. Payroll batches generation, pay, & salary structures management
        Route::prefix('payroll')->name('payroll.')->group(function (): void {
            Route::get('/', [PayrollController::class, 'index'])->name('index')->middleware('permission:payroll.view');
            Route::post('generate', [PayrollController::class, 'generate'])->name('generate')->middleware('permission:payroll.generate');
            Route::get('{id}/payslip', [PayrollController::class, 'payslip'])->name('payslip')->middleware('permission:payroll.view');
            Route::get('salary-structure/{employeeId}', [PayrollController::class, 'salaryStructure'])->name('salary-structure')->middleware('permission:payroll.view');
            Route::post('salary-structure', [PayrollController::class, 'updateSalaryStructure'])->name('salary-structure.update')->middleware('permission:payroll.generate');
            Route::patch('{id}/pay', [PayrollController::class, 'markAsPaid'])->name('pay')->middleware('permission:payroll.pay');
        });

        // 14. Reports & Analytics
        Route::prefix('reports')->name('reports.')->middleware('permission:reports.view')->group(function (): void {
            Route::get('attendance', [ReportController::class, 'attendance'])->name('attendance');
            Route::get('leaves', [ReportController::class, 'leaves'])->name('leaves');
            Route::get('payroll', [ReportController::class, 'payroll'])->name('payroll');
            Route::get('headcount', [ReportController::class, 'headcount'])->name('headcount');
        });

        // 15. Settings & Company Profile
        Route::prefix('settings')->name('settings.')->middleware('permission:settings.manage')->group(function (): void {
            Route::get('departments', [SettingController::class, 'departments'])->name('departments');
            Route::post('departments', [SettingController::class, 'storeDepartment'])->name('departments.store');
            Route::put('departments/{id}', [SettingController::class, 'updateDepartment'])->name('departments.update');
            Route::delete('departments/{id}', [SettingController::class, 'destroyDepartment'])->name('departments.destroy');

            Route::get('designations', [SettingController::class, 'designations'])->name('designations');
            Route::post('designations', [SettingController::class, 'storeDesignation'])->name('designations.store');
            Route::put('designations/{id}', [SettingController::class, 'updateDesignation'])->name('designations.update');
            Route::delete('designations/{id}', [SettingController::class, 'destroyDesignation'])->name('designations.destroy');

            Route::get('company', [SettingController::class, 'getCompanySettings'])->name('company');
            Route::put('company', [SettingController::class, 'updateCompanySettings'])->name('company.update');
        });

        // 16. Roles & Access Control
        Route::middleware('permission:roles.manage')->group(function (): void {
            Route::prefix('roles')->name('roles.')->group(function (): void {
                Route::get('permissions', [RolePermissionController::class, 'permissions'])->name('permissions');
                Route::post('assign-user', [RolePermissionController::class, 'assignUserRole'])->name('assign-user');
                Route::get('/', [RolePermissionController::class, 'index'])->name('index');
                Route::post('/', [RolePermissionController::class, 'store'])->name('store');
                Route::get('{id}', [RolePermissionController::class, 'show'])->name('show');
                Route::put('{id}', [RolePermissionController::class, 'update'])->name('update');
                Route::delete('{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
            });
        });
    });
});

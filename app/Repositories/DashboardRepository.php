<?php

namespace App\Repositories;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Carbon\Carbon;

class DashboardRepository
{
    public function getMetrics(): array
    {
        $today = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Employee & Department Metrics
        $totalEmployees = Employee::active()->count();
        $totalDepartments = Department::where('is_active', true)->count();

        // 2. Attendance Stats for Today
        $todayAttendancesQuery = Attendance::with(['employee.department', 'employee.designation'])
            ->where('attendance_date', $today);

        $todayAttendances = (clone $todayAttendancesQuery)->latest('id')->take(6)->get();

        $presentCount = (clone $todayAttendancesQuery)->where('status', Attendance::STATUS_PRESENT)->count();
        $lateCount = (clone $todayAttendancesQuery)->where('status', Attendance::STATUS_LATE)->count();
        $halfDayCount = (clone $todayAttendancesQuery)->where('status', Attendance::STATUS_HALF_DAY)->count();
        $presentTodayTotal = $presentCount + $lateCount + $halfDayCount;

        $onLeaveToday = LeaveRequest::where('status', LeaveRequest::STATUS_APPROVED)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        $absentToday = max(0, $totalEmployees - $presentTodayTotal - $onLeaveToday);
        $attendanceRate = $totalEmployees > 0 ? round(($presentTodayTotal / $totalEmployees) * 100, 1) : 0;

        // 3. Pending Requests & Approvals
        $pendingLeaves = LeaveRequest::where('status', LeaveRequest::STATUS_PENDING)->count();
        $pendingExpenses = ExpenseClaim::where('status', ExpenseClaim::STATUS_PENDING)->count();
        $pendingExpensesAmount = (float) ExpenseClaim::where('status', ExpenseClaim::STATUS_PENDING)->sum('amount');
        $totalPendingActions = $pendingLeaves + $pendingExpenses;

        // 4. Payroll Metrics
        $payrollSum = (float) Payroll::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'paid')
            ->sum('net_salary');

        // 5. Department Breakdown
        $departments = Department::where('is_active', true)
            ->withCount(['employees' => fn ($q) => $q->active()])
            ->get()
            ->map(function ($dept) use ($totalEmployees) {
                $count = $dept->employees_count ?? 0;
                $percentage = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100, 1) : 0;

                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            });

        // 6. Recent Actionable Data Collections
        $recentLeaveRequests = LeaveRequest::with(['employee.department', 'leave'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentExpenseClaims = ExpenseClaim::with('employee.department')
            ->latest('id')
            ->take(5)
            ->get();

        $recentAnnouncements = Announcement::with(['department', 'creator'])
            ->where('is_published', true)
            ->orderByDesc('is_pinned')
            ->latest('start_date')
            ->take(4)
            ->get();

        $upcomingHolidays = Holiday::where('holiday_date', '>=', $today)
            ->orderBy('holiday_date', 'asc')
            ->take(5)
            ->get();

        if ($upcomingHolidays->isEmpty()) {
            $upcomingHolidays = Holiday::where('holiday_year', $currentYear)
                ->orderBy('holiday_date', 'asc')
                ->take(5)
                ->get();
        }

        return [
            // Backward-compatible keys
            'total_employees' => $totalEmployees,
            'present_today' => $presentTodayTotal,
            'on_leave_today' => $onLeaveToday,
            'pending_leave_requests' => $pendingLeaves,
            'pending_expense_claims' => $pendingExpenses,
            'monthly_payroll_disbursed' => $payrollSum,

            // Rich Metrics
            'total_departments' => $totalDepartments,
            'present_count' => $presentCount,
            'late_today' => $lateCount,
            'half_day_today' => $halfDayCount,
            'absent_today' => $absentToday,
            'attendance_rate' => $attendanceRate,
            'pending_leaves_count' => $pendingLeaves,
            'pending_expenses_count' => $pendingExpenses,
            'pending_expenses_amount' => $pendingExpensesAmount,
            'total_pending_actions' => $totalPendingActions,

            // Collections & Arrays
            'department_distribution' => $departments,
            'today_attendances' => $todayAttendances,
            'recent_leave_requests' => $recentLeaveRequests,
            'recent_expense_claims' => $recentExpenseClaims,
            'recent_announcements' => $recentAnnouncements,
            'upcoming_holidays' => $upcomingHolidays,
        ];
    }
}

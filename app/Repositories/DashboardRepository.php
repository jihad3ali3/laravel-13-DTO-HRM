<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExpenseClaim;
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

        $totalEmployees = Employee::active()->count();

        $presentToday = Attendance::where('attendance_date', $today)
            ->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE, Attendance::STATUS_HALF_DAY])
            ->count();

        $onLeaveToday = LeaveRequest::where('status', LeaveRequest::STATUS_APPROVED)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        $pendingLeaves = LeaveRequest::where('status', LeaveRequest::STATUS_PENDING)->count();

        $pendingExpenses = ExpenseClaim::where('status', ExpenseClaim::STATUS_PENDING)->count();

        $payrollSum = (float) Payroll::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('status', 'paid')
            ->sum('net_salary');

        return [
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'on_leave_today' => $onLeaveToday,
            'pending_leave_requests' => $pendingLeaves,
            'pending_expense_claims' => $pendingExpenses,
            'monthly_payroll_disbursed' => $payrollSum,
        ];
    }
}

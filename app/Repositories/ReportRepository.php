<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Carbon\Carbon;

class ReportRepository
{
    public function attendance(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->toDateString();

        $records = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->get();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_logs' => $records->count(),
            'present' => $records->where('status', Attendance::STATUS_PRESENT)->count(),
            'late' => $records->where('status', Attendance::STATUS_LATE)->count(),
            'half_day' => $records->where('status', Attendance::STATUS_HALF_DAY)->count(),
            'absent' => $records->where('status', Attendance::STATUS_ABSENT)->count(),
        ];
    }

    public function leaves(): array
    {
        $currentYear = Carbon::now()->year;
        $totalRequests = LeaveRequest::whereYear('start_date', $currentYear)->count();
        $approved = LeaveRequest::whereYear('start_date', $currentYear)->where('status', LeaveRequest::STATUS_APPROVED)->count();
        $pending = LeaveRequest::whereYear('start_date', $currentYear)->where('status', LeaveRequest::STATUS_PENDING)->count();
        $rejected = LeaveRequest::whereYear('start_date', $currentYear)->where('status', LeaveRequest::STATUS_REJECTED)->count();

        return [
            'year' => $currentYear,
            'total_requests' => $totalRequests,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
        ];
    }

    public function payroll(): array
    {
        $currentYear = Carbon::now()->year;
        $records = Payroll::where('year', $currentYear)->get();

        $monthlyDisbursed = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthSum = (float) $records->where('month', $m)->where('status', 'paid')->sum('net_salary');
            $monthlyDisbursed[$m] = $monthSum;
        }

        return [
            'year' => $currentYear,
            'total_annual_disbursed' => array_sum($monthlyDisbursed),
            'monthly_breakdown' => $monthlyDisbursed,
        ];
    }

    public function headcount(): array
    {
        $departments = Department::withCount(['employees' => function ($q) {
            $q->active();
        }])->get();

        $breakdown = [];
        foreach ($departments as $dept) {
            $breakdown[$dept->name] = $dept->employees_count;
        }

        return [
            'total_active_headcount' => Employee::active()->count(),
            'department_breakdown' => $breakdown,
        ];
    }
}

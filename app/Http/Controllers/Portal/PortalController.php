<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PerformanceGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        // 1. Today's Attendance Record
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        // 2. Monthly Working Stats
        $monthlyAttendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', Carbon::now()->month)
            ->whereYear('attendance_date', Carbon::now()->year)
            ->get();

        $presentDays = $monthlyAttendances->where('status', Attendance::STATUS_PRESENT)->count();
        $lateDays = $monthlyAttendances->where('status', Attendance::STATUS_LATE)->count();
        $totalHours = (float) $monthlyAttendances->sum('working_hours');

        // 3. Leave Balances
        $leaveTypes = Leave::where('is_active', true)->get();
        $usedLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('start_date', Carbon::now()->year)
            ->count();

        // 4. Pending Claims
        $pendingClaims = ExpenseClaim::where('employee_id', $employee->id)
            ->where('status', ExpenseClaim::STATUS_PENDING)
            ->count();

        // 5. Active Goals
        $activeGoals = PerformanceGoal::where('employee_id', $employee->id)
            ->where('status', 'in_progress')
            ->get();

        // 6. Latest Payslip
        $latestPayroll = Payroll::where('employee_id', $employee->id)
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        // 7. Recent Notices & Upcoming Holidays
        $notices = Announcement::where('is_published', true)
            ->latest('start_date')
            ->take(3)
            ->get();

        $holidays = Holiday::where('holiday_year', Carbon::now()->year)
            ->where('holiday_date', '>=', Carbon::now()->toDateString())
            ->orderBy('holiday_date')
            ->take(3)
            ->get();

        return view('portal.dashboard', compact(
            'employee',
            'todayAttendance',
            'presentDays',
            'lateDays',
            'totalHours',
            'leaveTypes',
            'usedLeaves',
            'pendingClaims',
            'activeGoals',
            'latestPayroll',
            'notices',
            'holidays'
        ));
    }
}

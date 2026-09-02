<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckInRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckoutRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceFilterRequest;
use App\Http\Services\AttendanceService;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(AttendanceFilterRequest $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $departmentId = $request->input('department_id');

        $query = Attendance::with(['employee.department', 'employee.designation'])
            ->where('attendance_date', $selectedDate);

        if ($departmentId) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $departmentId));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('id')->paginate(20);

        $presentCount = (clone $query)->where('status', Attendance::STATUS_PRESENT)->count();
        $lateCount = (clone $query)->where('status', Attendance::STATUS_LATE)->count();
        $halfDayCount = (clone $query)->where('status', Attendance::STATUS_HALF_DAY)->count();
        $absentCount = (clone $query)->where('status', Attendance::STATUS_ABSENT)->count();

        $departments = Department::where('is_active', true)->get();
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('admin.attendance.index', compact(
            'attendances',
            'departments',
            'employees',
            'selectedDate',
            'presentCount',
            'lateCount',
            'halfDayCount',
            'absentCount'
        ));
    }

    public function checkIn(AttendanceCheckInRequest $request): RedirectResponse
    {
        $this->attendanceService->checkIn($request->toDto());

        return redirect()->back()->with('success', 'Employee checked in successfully.');
    }

    public function checkOut(AttendanceCheckoutRequest $request): RedirectResponse
    {
        $this->attendanceService->checkOut($request);

        return redirect()->back()->with('success', 'Employee checked out successfully.');
    }
}

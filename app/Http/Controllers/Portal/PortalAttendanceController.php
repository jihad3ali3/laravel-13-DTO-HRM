<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Attendance\AttendanceCheckInDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckoutRequest;
use App\Http\Services\AttendanceService;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalAttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $selectedMonth = (int) $request->input('month', Carbon::now()->month);
        $selectedYear = (int) $request->input('year', Carbon::now()->year);

        $logs = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $selectedMonth)
            ->whereYear('attendance_date', $selectedYear)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        $shift = EmployeeShift::where('employee_id', $employee->id)
            ->with('shift')
            ->active()
            ->first();

        return view('portal.attendance.index', compact(
            'employee',
            'logs',
            'todayAttendance',
            'shift',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function checkIn(): RedirectResponse
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $dto = new AttendanceCheckInDto(
            employeeId: $employee->id
        );

        $this->attendanceService->checkIn($dto);

        return redirect()->back()->with('success', 'Clocked in successfully at '.Carbon::now()->format('h:i A'));
    }

    public function checkOut(): RedirectResponse
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $request = new AttendanceCheckoutRequest;
        $request->merge([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::now()->toDateString(),
            'check_out' => Carbon::now()->toTimeString(),
        ]);

        $this->attendanceService->checkOut($request);

        return redirect()->back()->with('success', 'Clocked out successfully at '.Carbon::now()->format('h:i A'));
    }
}

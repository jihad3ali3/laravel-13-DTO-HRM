<?php

namespace App\Repositories;

use App\Domain\Attendance\AttendanceCheckInDto;
use App\Domain\Attendance\AttendanceCheckoutDto;
use App\Domain\Attendance\AttendanceFilterDto;
use App\Exceptions\RepositoryException;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AttendanceRepository
{
    public function getLogs(AttendanceFilterDto $attendanceFilterDto): LengthAwarePaginator
    {
        return Attendance::query()
            ->with([
                'employee:id,employee_code,first_name,last_name,department_id',
                'employee.department:id,name',
            ])
            ->when(
                $attendanceFilterDto->employeeId,
                fn ($query, $empId) => $query->where('employee_id', $empId)
            )
            ->when(
                $attendanceFilterDto->fromDate,
                fn ($query, $fromDate) => $query->where('created_at', '>=', $fromDate)
            )
            ->when(
                $attendanceFilterDto->toDate,
                fn ($query, $toDate) => $query->where('created_at', '<=', $toDate)
            )
            ->when(
                $attendanceFilterDto->departmentId,
                fn ($query, $departmentId) => $query->whereHas(
                    'employee',
                    fn ($query) => $query->where('department_id', $departmentId)
                )
            )
            ->when(
                $attendanceFilterDto->status,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->latest()
            ->paginate($attendanceFilterDto->perPage);
    }

    /**
     * @throws RepositoryException
     */
    public function checkIn(AttendanceCheckInDto $attendanceCheckInDto): Attendance
    {
        $employeeShift = EmployeeShift::where('employee_id', $attendanceCheckInDto->employeeId)
            ->with(['shift'])
            ->active()
            ->where('start_date', '<=', Carbon::now()->toDateString())
            ->where(function ($query) {
                $query->where('end_date', '>=', Carbon::now()->toDateString())->orWhereNull('end_date');
            })
            ->first();

        if (! $employeeShift) {
            throw new RepositoryException('Employee shift not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $currentStatus = Attendance::STATUS_PRESENT;
        $shiftStartTime = Carbon::parse($employeeShift->shift->start_time)->setDate(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day);
        $shiftEndTime = Carbon::parse($employeeShift->shift->end_time)->setDate(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day);

        if (Carbon::now()->gt($shiftEndTime)) {
            throw new RepositoryException('Your shift already ended at '.$shiftEndTime, ResponseAlias::HTTP_NOT_FOUND);
        }

        if (Carbon::now()->gt($shiftStartTime)) {
            $differenceInMinutes = abs($shiftStartTime->diffInMinutes($shiftEndTime, true));
            $differenceInHours = abs($shiftStartTime->diffInHours($shiftEndTime, true));
            if ($differenceInHours == 0 && $differenceInMinutes > $employeeShift->shift->mark_late_after_minutes) {
                $currentStatus = Attendance::STATUS_LATE;
            } elseif ($differenceInHours > 0) {
                if ($differenceInHours > ($employeeShift->shift->totalHours / 2)) {
                    $currentStatus = Attendance::STATUS_HALF_DAY;
                }
            }
        }

        $attendance = new Attendance;
        $attendance->employee_id = $attendanceCheckInDto->employeeId;
        $attendance->attendance_date = Carbon::now()->toDateString();
        $attendance->check_in = Carbon::now()->toTimeString();
        $attendance->status = $currentStatus;
        $attendance->save();

        return $attendance;
    }

    public function checkOut(AttendanceCheckoutDto $attendanceCheckOutDto): Attendance
    {
        return DB::transaction(function () use ($attendanceCheckOutDto) {
            $attendance = Attendance::where('employee_id', $attendanceCheckOutDto->employeeId)
                ->whereDate('attendance_date', Carbon::now()->toDateString())
                ->whereNull('check_out')
                ->lockForUpdate()
                ->first();
            if (! $attendance) {
                throw new RepositoryException('Attendance not found', ResponseAlias::HTTP_NOT_FOUND);
            }
            $attendance->check_out = Carbon::now()->toTimeString();
            $attendance->working_hours = Carbon::now()->diffInHours(Carbon::parse($attendance->check_in)->setDate(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day));
            $attendance->save();

            return $attendance;
        });
    }

    public function dailySummary(string $date): array
    {
        $totalEmployees = Employee::query()->active()->count();
        $records = Attendance::query()->whereDate('date', $date)->get();

        $presentCount = $records->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count();
        $lateCount = $records->where('status', Attendance::STATUS_LATE)->count();
        $halfDayCount = $records->where('status', Attendance::STATUS_HALF_DAY)->count();
        $onLeaveCount = $records->where('status', Attendance::STATUS_ABSENT)->count();
        $absentCount = max(0, $totalEmployees - $presentCount - $halfDayCount - $onLeaveCount);

        return [
            'date' => $date,
            'total_employees' => $totalEmployees,
            'present' => $presentCount,
            'late' => $lateCount,
            'half_day' => $halfDayCount,
            'on_leave' => $onLeaveCount,
            'absent' => $absentCount,
        ];
    }

    public function monthlySummary(int $employeeId, int $month, int $year): array
    {
        $attendances = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $presentDays = $attendances->where('status', Attendance::STATUS_PRESENT)->count();
        $lateDays = $attendances->where('status', Attendance::STATUS_LATE)->count();
        $halfDays = $attendances->where('status', Attendance::STATUS_HALF_DAY)->count();
        $onLeaveDays = $attendances->where('status', Attendance::STATUS_ABSENT)->count();

        // Calculate workdays in the month (weekdays)
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        if ($endDate->isFuture()) {
            $endDate = Carbon::today();
        }

        $workdays = 0;
        if ($startDate->lte($endDate)) {
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if (! $date->isWeekend()) {
                    $workdays++;
                }
            }
        }

        $effectivePresent = $presentDays + $lateDays + ($halfDays * 0.5) + $onLeaveDays;
        $percentage = $workdays > 0 ? min(100, round(($effectivePresent / $workdays) * 100, 0)) : 100;

        return [
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'total_working_days' => $attendances->count(),
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'half_days' => $halfDays,
            'total_working_hours' => round((float) $attendances->sum('working_hours'), 1),
            'attendance_percentage' => "{$percentage}%",
            'logs' => $attendances,
        ];
    }
}

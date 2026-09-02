<?php

namespace App\Http\Services;

use App\Domain\Attendance\AttendanceCheckInDto;
use App\Domain\Attendance\AttendanceFilterDto;
use App\Exceptions\RepositoryException;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckoutRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceSummaryRequest;
use App\Models\Attendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class AttendanceService
{
    public function __construct(private AttendanceRepository $attendanceRepository) {}

    public function getLogs(AttendanceFilterDto $attendanceFilterDto): LengthAwarePaginator
    {
        return $this->attendanceRepository->getLogs($attendanceFilterDto);
    }

    /**
     * @throws RepositoryException
     */
    public function checkIn(AttendanceCheckInDto $attendanceCheckInDto): Attendance
    {
        return $this->attendanceRepository->checkIn($attendanceCheckInDto);
    }

    public function checkOut(AttendanceCheckoutRequest $request): Attendance
    {
        return $this->attendanceRepository->checkOut($request->toDto());
    }

    public function dailySummary(AttendanceSummaryRequest $request)
    {
        return $this->attendanceRepository->dailySummary($request->toDto());
    }

    public function monthlySummary(AttendanceSummaryRequest $request)
    {
        return $this->attendanceRepository->monthlySummary($request->toDto());
    }
}

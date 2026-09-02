<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckInRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceCheckoutRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceFilterRequest;
use App\Http\Requests\Api\V1\Attendance\AttendanceSummaryRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(AttendanceFilterRequest $request)
    {
        $logs = $this->attendanceService->getLogs($request->toDto());

        return ApiResponse::success($logs, 'Attendance logs retrieved successfully.');
    }

    public function checkIn(AttendanceCheckInRequest $request)
    {
        try {
            $attendance = $this->attendanceService->checkIn($request->toDto());

            return ApiResponse::success($attendance, 'Checked-In successfully.');
        } catch (RepositoryException $repositoryException) {
            return ApiResponse::error($repositoryException->getMessage(), $repositoryException->getCode());
        }
    }

    public function checkOut(AttendanceCheckoutRequest $request)
    {
        $logs = $this->attendanceService->checkOut($request->toDto());

        return ApiResponse::success($logs, 'Checked-Out successfully.');
    }

    public function dailySummary(AttendanceSummaryRequest $request)
    {
        $logs = $this->attendanceService->dailySummary($request->toDto());

        return ApiResponse::success($logs, 'Daily summary retrieved successfully.');
    }

    public function monthlySummary(AttendanceSummaryRequest $request)
    {
        $logs = $this->attendanceService->monthlySummary($request->toDto());

        return ApiResponse::success($logs, 'Attendance logs retrieved successfully.');
    }
}

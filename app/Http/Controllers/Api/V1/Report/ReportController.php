<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function attendance(Request $request): JsonResponse
    {
        $result = $this->reportService->attendance(
            $request->query('start_date'),
            $request->query('end_date')
        );

        return ApiResponse::success($result, 'Attendance report generated successfully.');
    }

    public function leaves(): JsonResponse
    {
        $result = $this->reportService->leaves();

        return ApiResponse::success($result, 'Leaves report generated successfully.');
    }

    public function payroll(): JsonResponse
    {
        $result = $this->reportService->payroll();

        return ApiResponse::success($result, 'Payroll report generated successfully.');
    }

    public function headcount(): JsonResponse
    {
        $result = $this->reportService->headcount();

        return ApiResponse::success($result, 'Headcount report generated successfully.');
    }
}

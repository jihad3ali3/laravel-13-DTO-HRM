<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function metrics(): JsonResponse
    {
        $metrics = $this->dashboardService->getMetrics();

        return ApiResponse::success($metrics, 'Dashboard metrics retrieved successfully.');
    }
}

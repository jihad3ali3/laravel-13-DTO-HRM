<?php

namespace App\Http\Controllers\Api\V1\Performance;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Performance\PerformanceGoalProgressRequest;
use App\Http\Requests\Api\V1\Performance\PerformanceGoalStoreRequest;
use App\Http\Requests\Api\V1\Performance\PerformanceReviewStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\PerformanceService;
use Illuminate\Http\JsonResponse;

class PerformanceController extends Controller
{
    public function __construct(private readonly PerformanceService $performanceService) {}

    public function goals(int $employeeId): JsonResponse
    {
        $goals = $this->performanceService->getGoals($employeeId);

        return ApiResponse::success($goals, 'Performance goals retrieved successfully.');
    }

    public function updateGoalProgress(int $id, PerformanceGoalProgressRequest $request): JsonResponse
    {
        try {
            $goal = $this->performanceService->updateGoalProgress($id, $request->toDto());

            return ApiResponse::success($goal, 'Goal progress updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function storeGoal(PerformanceGoalStoreRequest $request): JsonResponse
    {
        $goal = $this->performanceService->storeGoal($request->toDto());

        return ApiResponse::created($goal, 'Performance goal created successfully.');
    }

    public function destroyGoal(int $id): JsonResponse
    {
        try {
            $this->performanceService->destroyGoal($id);

            return ApiResponse::success(null, 'Performance goal deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function reviews(): JsonResponse
    {
        $user = auth()->user();
        $employeeId = null;
        if ($user && ! $user->hasAnyPermission(['performance.manage', 'employees.edit']) && $user->employee) {
            $employeeId = $user->employee->id;
        }

        $reviews = $this->performanceService->getReviews($employeeId);

        return ApiResponse::success($reviews, 'Performance reviews retrieved successfully.');
    }

    public function showReview(int $id): JsonResponse
    {
        try {
            $review = $this->performanceService->showReview($id);

            return ApiResponse::success($review, 'Performance review details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function storeReview(PerformanceReviewStoreRequest $request): JsonResponse
    {
        $review = $this->performanceService->storeReview($request->toDto());

        return ApiResponse::created($review, 'Performance review saved successfully.');
    }
}

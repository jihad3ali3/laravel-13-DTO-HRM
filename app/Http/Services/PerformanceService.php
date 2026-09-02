<?php

namespace App\Http\Services;

use App\Domain\Performance\PerformanceGoalProgressDto;
use App\Domain\Performance\PerformanceGoalStoreDto;
use App\Domain\Performance\PerformanceReviewStoreDto;
use App\Repositories\PerformanceRepository;

readonly class PerformanceService
{
    public function __construct(private PerformanceRepository $performanceRepository) {}

    public function getGoals(int $employeeId)
    {
        return $this->performanceRepository->getGoals($employeeId);
    }

    public function updateGoalProgress(int $id, PerformanceGoalProgressDto $dto)
    {
        return $this->performanceRepository->updateGoalProgress($id, $dto);
    }

    public function storeGoal(PerformanceGoalStoreDto $dto)
    {
        return $this->performanceRepository->storeGoal($dto);
    }

    public function destroyGoal(int $id): void
    {
        $this->performanceRepository->destroyGoal($id);
    }

    public function getReviews(?int $employeeId = null)
    {
        return $this->performanceRepository->getReviews($employeeId);
    }

    public function showReview(int $id)
    {
        return $this->performanceRepository->showReview($id);
    }

    public function storeReview(PerformanceReviewStoreDto $dto)
    {
        return $this->performanceRepository->storeReview($dto);
    }
}

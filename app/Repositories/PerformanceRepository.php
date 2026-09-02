<?php

namespace App\Repositories;

use App\Domain\Performance\PerformanceGoalProgressDto;
use App\Domain\Performance\PerformanceGoalStoreDto;
use App\Domain\Performance\PerformanceReviewStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\PerformanceGoal;
use App\Models\PerformanceReview;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PerformanceRepository
{
    public function getGoals(int $employeeId)
    {
        return PerformanceGoal::where('employee_id', $employeeId)->latest()->get();
    }

    /**
     * @throws RepositoryException
     */
    public function updateGoalProgress(int $id, PerformanceGoalProgressDto $dto): PerformanceGoal
    {
        $goal = PerformanceGoal::find($id);
        if (! $goal) {
            throw new RepositoryException('Performance goal not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $status = $goal->status;
        if ($dto->progress === 100) {
            $status = 'completed';
        } elseif ($dto->progress > 0 && $goal->status === 'not_started') {
            $status = 'in_progress';
        }

        $goal->update([
            'progress' => $dto->progress,
            'status' => $status,
        ]);

        return $goal;
    }

    public function storeGoal(PerformanceGoalStoreDto $dto): PerformanceGoal
    {
        return PerformanceGoal::create([
            'employee_id' => $dto->employeeId,
            'title' => $dto->title,
            'description' => $dto->description,
            'start_date' => $dto->startDate,
            'due_date' => $dto->dueDate,
            'priority' => $dto->priority,
            'status' => 'not_started',
            'progress' => 0,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function destroyGoal(int $id): void
    {
        $goal = PerformanceGoal::find($id);
        if (! $goal) {
            throw new RepositoryException('Performance goal not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $goal->delete();
    }

    public function getReviews(?int $employeeId = null)
    {
        return PerformanceReview::query()
            ->with(['employee:id,first_name,last_name,employee_code', 'reviewer:id,name'])
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->latest()
            ->get();
    }

    /**
     * @throws RepositoryException
     */
    public function showReview(int $id): PerformanceReview
    {
        $review = PerformanceReview::with(['employee', 'reviewer'])->find($id);
        if (! $review) {
            throw new RepositoryException('Performance review not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $review;
    }

    public function storeReview(PerformanceReviewStoreDto $dto): PerformanceReview
    {
        return PerformanceReview::create([
            'employee_id' => $dto->employeeId,
            'reviewer_id' => $dto->reviewerId,
            'review_cycle' => $dto->reviewCycle,
            'rating' => $dto->rating,
            'strengths' => $dto->strengths,
            'area_of_improvements' => $dto->areaOfImprovements,
            'employee_feedback' => $dto->employeeFeedback,
            'status' => $dto->status,
            'reviewed_at' => $dto->status === 'submitted' ? Carbon::now() : null,
        ]);
    }
}

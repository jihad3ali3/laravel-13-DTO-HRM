<?php

namespace App\Http\Controllers\Admin\Performance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Performance\PerformanceGoalProgressRequest;
use App\Http\Requests\Api\V1\Performance\PerformanceGoalStoreRequest;
use App\Http\Requests\Api\V1\Performance\PerformanceReviewStoreRequest;
use App\Http\Services\PerformanceService;
use App\Models\Employee;
use App\Models\PerformanceGoal;
use App\Models\PerformanceReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function __construct(private readonly PerformanceService $performanceService) {}

    public function goals(Request $request): View
    {
        $query = PerformanceGoal::with(['employee.department', 'employee.designation']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $goals = $query->latest('id')->paginate(15);
        $employees = Employee::active()->orderBy('first_name')->get();

        $inProgressCount = PerformanceGoal::where('status', 'in_progress')->count();
        $completedCount = PerformanceGoal::where('status', 'completed')->count();
        $notStartedCount = PerformanceGoal::where('status', 'not_started')->count();

        return view('admin.performance.goals', compact(
            'goals',
            'employees',
            'inProgressCount',
            'completedCount',
            'notStartedCount'
        ));
    }

    public function storeGoal(PerformanceGoalStoreRequest $request): RedirectResponse
    {
        $this->performanceService->storeGoal($request->toDto());

        return redirect()->back()->with('success', 'Performance goal established successfully.');
    }

    public function updateGoalProgress(int $id, PerformanceGoalProgressRequest $request): RedirectResponse
    {
        $this->performanceService->updateGoalProgress($id, $request->toDto());

        return redirect()->back()->with('success', 'Goal progress updated successfully.');
    }

    public function destroyGoal(int $id): RedirectResponse
    {
        $this->performanceService->destroyGoal($id);

        return redirect()->back()->with('success', 'Performance goal removed successfully.');
    }

    public function reviews(Request $request): View
    {
        $query = PerformanceReview::with(['employee.department', 'employee.designation', 'reviewer']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $reviews = $query->latest('id')->paginate(15);
        $employees = Employee::active()->orderBy('first_name')->get();
        $avgRating = round((float) PerformanceReview::avg('rating'), 1);

        return view('admin.performance.reviews', compact('reviews', 'employees', 'avgRating'));
    }

    public function storeReview(PerformanceReviewStoreRequest $request): RedirectResponse
    {
        $this->performanceService->storeReview($request->toDto());

        return redirect()->back()->with('success', 'Performance appraisal recorded successfully.');
    }
}

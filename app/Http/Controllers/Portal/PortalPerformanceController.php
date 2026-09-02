<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Performance\PerformanceGoalProgressDto;
use App\Http\Controllers\Controller;
use App\Http\Services\PerformanceService;
use App\Models\Employee;
use App\Models\PerformanceGoal;
use App\Models\PerformanceReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalPerformanceController extends Controller
{
    public function __construct(private readonly PerformanceService $performanceService) {}

    public function index(): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $goals = PerformanceGoal::where('employee_id', $employee->id)->latest('id')->get();
        $reviews = PerformanceReview::where('employee_id', $employee->id)->with('reviewer')->latest('id')->get();

        $completedCount = $goals->where('status', 'completed')->count();
        $inProgressCount = $goals->where('status', 'in_progress')->count();

        return view('portal.performance.index', compact('employee', 'goals', 'reviews', 'completedCount', 'inProgressCount'));
    }

    public function updateProgress(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $goal = PerformanceGoal::where('employee_id', $employee->id)->findOrFail($id);

        $dto = new PerformanceGoalProgressDto(
            progress: (int) $request->progress,
            status: $request->status
        );

        $this->performanceService->updateGoalProgress($goal->id, $dto);

        return redirect()->back()->with('success', 'Goal progress updated successfully.');
    }
}

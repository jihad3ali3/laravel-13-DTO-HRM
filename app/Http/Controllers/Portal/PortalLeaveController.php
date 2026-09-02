<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Leave\LeaveRequestStoreDto;
use App\Http\Controllers\Controller;
use App\Http\Services\LeaveService;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalLeaveController extends Controller
{
    public function __construct(private readonly LeaveService $leaveService) {}

    public function index(): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $leaveRequests = LeaveRequest::with('leave')
            ->where('employee_id', $employee->id)
            ->latest('id')
            ->paginate(15);

        $leaveTypes = Leave::where('is_active', true)->get();

        // Calculate balances
        $balances = [];
        foreach ($leaveTypes as $lt) {
            $used = LeaveRequest::where('employee_id', $employee->id)
                ->where('leave_id', $lt->id)
                ->where('status', LeaveRequest::STATUS_APPROVED)
                ->whereYear('start_date', Carbon::now()->year)
                ->count();

            $balances[$lt->id] = [
                'type' => $lt,
                'allowed' => $lt->max_day_per_year,
                'used' => $used,
                'remaining' => max(0, $lt->max_day_per_year - $used),
            ];
        }

        return view('portal.leaves.index', compact('employee', 'leaveRequests', 'leaveTypes', 'balances'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'leave_id' => 'required|exists:leaves,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $dto = new LeaveRequestStoreDto(
            employeeId: $employee->id,
            leaveId: (int) $request->leave_id,
            startDate: $request->start_date,
            endDate: $request->end_date,
            reason: $request->reason
        );

        $this->leaveService->store($dto);

        return redirect()->back()->with('success', 'Leave request submitted to HR.');
    }
}

<?php

namespace App\Http\Controllers\Admin\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Leave\LeaveFilterRequest;
use App\Http\Requests\Api\V1\Leave\LeaveRequestProcessRequest;
use App\Http\Requests\Api\V1\Leave\LeaveRequestStoreRequest;
use App\Http\Services\LeaveService;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(private readonly LeaveService $leaveService) {}

    public function requests(LeaveFilterRequest $request): View
    {
        $leaveRequests = $this->leaveService->getRequests($request->toDto());
        $leaveTypes = $this->leaveService->getTypes();
        $employees = Employee::active()->orderBy('first_name')->get();

        $pendingCount = LeaveRequest::where('status', LeaveRequest::STATUS_PENDING)->count();
        $approvedCount = LeaveRequest::where('status', LeaveRequest::STATUS_APPROVED)->count();
        $rejectedCount = LeaveRequest::where('status', LeaveRequest::STATUS_REJECTED)->count();

        return view('admin.leaves.requests', compact(
            'leaveRequests',
            'leaveTypes',
            'employees',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function store(LeaveRequestStoreRequest $request): RedirectResponse
    {
        $this->leaveService->store($request->toDto());

        return redirect()->back()->with('success', 'Leave request submitted successfully.');
    }

    public function process(int $id, LeaveRequestProcessRequest $request): RedirectResponse
    {
        $this->leaveService->process($id, $request->toDto());

        return redirect()->back()->with('success', 'Leave request has been '.ucfirst($request->status).'.');
    }

    public function types(): View
    {
        $leaveTypes = Leave::all();

        return view('admin.leaves.types', compact('leaveTypes'));
    }
}

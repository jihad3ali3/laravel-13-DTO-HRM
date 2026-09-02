<?php

namespace App\Http\Controllers\Admin\Shift;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Shift\ShiftAssignRequest;
use App\Http\Requests\Api\V1\Shift\ShiftStoreRequest;
use App\Http\Services\ShiftService;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function __construct(private readonly ShiftService $shiftService) {}

    public function index(): View
    {
        $shifts = $this->shiftService->getShifts();
        $employeeShifts = EmployeeShift::with(['employee.department', 'shift'])->latest('id')->paginate(15);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('admin.shifts.index', compact('shifts', 'employeeShifts', 'employees'));
    }

    public function store(ShiftStoreRequest $request): RedirectResponse
    {
        $this->shiftService->store($request->toDto());

        return redirect()->back()->with('success', 'Shift schedule created successfully.');
    }

    public function update(int $id, ShiftStoreRequest $request): RedirectResponse
    {
        $this->shiftService->update($id, $request->toDto());

        return redirect()->back()->with('success', 'Shift schedule updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->shiftService->destroy($id);

        return redirect()->back()->with('success', 'Shift schedule removed successfully.');
    }

    public function assign(ShiftAssignRequest $request): RedirectResponse
    {
        $this->shiftService->assign($request->toDto());

        return redirect()->back()->with('success', 'Shift assigned to employee successfully.');
    }
}

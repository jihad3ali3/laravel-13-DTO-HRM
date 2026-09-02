<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\EmployeeFilterRequest;
use App\Http\Requests\Api\V1\Employee\EmployeeStoreRequest;
use App\Http\Requests\Api\V1\Employee\EmployeeUpdateRequest;
use App\Http\Services\EmployeeService;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    public function index(EmployeeFilterRequest $request): View
    {
        $employees = $this->employeeService->getEmployees($request->toDto());
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();

        return view('admin.employees.index', compact('employees', 'departments', 'designations'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();
        $roles = Role::all();

        return view('admin.employees.create', compact('departments', 'designations', 'roles'));
    }

    public function store(EmployeeStoreRequest $request): RedirectResponse
    {
        $this->employeeService->store($request->toDto());

        return redirect()->route('admin::employees.index')->with('success', 'Employee registered successfully.');
    }

    public function show(int $id): View
    {
        $employee = Employee::with([
            'user',
            'department',
            'designation',
            'attendances' => fn ($q) => $q->latest('attendance_date')->take(10),
            'leaveRequests' => fn ($q) => $q->latest('created_at')->take(10),
            'documents',
            'salaryStructure',
            'employeeShifts.shift',
        ])->findOrFail($id);

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(int $id): View
    {
        $employee = Employee::with(['user', 'department', 'designation'])->findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();

        return view('admin.employees.edit', compact('employee', 'departments', 'designations'));
    }

    public function update(int $id, EmployeeUpdateRequest $request): RedirectResponse
    {
        $this->employeeService->update($id, $request->toDto());

        return redirect()->route('admin::employees.show', $id)->with('success', 'Employee details updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->employeeService->destroy($id);

        return redirect()->route('admin::employees.index')->with('success', 'Employee record deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Employee\EmployeeFilterRequest;
use App\Http\Requests\Api\V1\Employee\EmployeeStoreRequest;
use App\Http\Requests\Api\V1\Employee\EmployeeUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\EmployeeService;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    public function index(EmployeeFilterRequest $request): JsonResponse
    {
        $employees = $this->employeeService->getEmployees($request->toDto());

        return ApiResponse::success($employees, 'Employees list retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $employee = $this->employeeService->show($id);

            return ApiResponse::success($employee, 'Employee details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(EmployeeStoreRequest $request): JsonResponse
    {
        $employee = $this->employeeService->store($request->toDto());

        return ApiResponse::created($employee, 'Employee registered successfully.');
    }

    public function update(int $id, EmployeeUpdateRequest $request): JsonResponse
    {
        try {
            $employee = $this->employeeService->update($id, $request->toDto());

            return ApiResponse::success($employee, 'Employee updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->employeeService->destroy($id);

            return ApiResponse::success(null, 'Employee record deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}

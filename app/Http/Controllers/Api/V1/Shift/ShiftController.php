<?php

namespace App\Http\Controllers\Api\V1\Shift;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Shift\ShiftAssignRequest;
use App\Http\Requests\Api\V1\Shift\ShiftStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\ShiftService;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    public function __construct(private readonly ShiftService $shiftService) {}

    public function employeeShift(int $employeeId): JsonResponse
    {
        $shifts = $this->shiftService->getEmployeeShifts($employeeId);

        return ApiResponse::success($shifts, 'Employee shifts retrieved successfully.');
    }

    public function index(): JsonResponse
    {
        $shifts = $this->shiftService->getShifts();

        return ApiResponse::success($shifts, 'Shifts retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $shift = $this->shiftService->show($id);

            return ApiResponse::success($shift, 'Shift details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(ShiftStoreRequest $request): JsonResponse
    {
        $shift = $this->shiftService->store($request->toDto());

        return ApiResponse::created($shift, 'Shift created successfully.');
    }

    public function update(int $id, ShiftStoreRequest $request): JsonResponse
    {
        try {
            $shift = $this->shiftService->update($id, $request->toDto());

            return ApiResponse::success($shift, 'Shift updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->shiftService->destroy($id);

            return ApiResponse::success(null, 'Shift deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function assign(ShiftAssignRequest $request): JsonResponse
    {
        $employeeShift = $this->shiftService->assign($request->toDto());

        return ApiResponse::success($employeeShift, 'Shift assigned to employee successfully.');
    }
}

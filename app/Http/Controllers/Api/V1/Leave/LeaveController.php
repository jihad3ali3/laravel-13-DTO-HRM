<?php

namespace App\Http\Controllers\Api\V1\Leave;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Leave\LeaveFilterRequest;
use App\Http\Requests\Api\V1\Leave\LeaveRequestProcessRequest;
use App\Http\Requests\Api\V1\Leave\LeaveRequestStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\LeaveService;
use Illuminate\Http\JsonResponse;

class LeaveController extends Controller
{
    public function __construct(private readonly LeaveService $leaveService) {}

    public function types(): JsonResponse
    {
        $types = $this->leaveService->getTypes();

        return ApiResponse::success($types, 'Leave types retrieved successfully.');
    }

    public function requests(LeaveFilterRequest $request): JsonResponse
    {
        $requests = $this->leaveService->getRequests($request->toDto());

        return ApiResponse::success($requests, 'Leave requests retrieved successfully.');
    }

    public function store(LeaveRequestStoreRequest $request): JsonResponse
    {
        try {
            $leaveRequest = $this->leaveService->store($request->toDto());

            return ApiResponse::created($leaveRequest, 'Leave request submitted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function balances(int $employeeId): JsonResponse
    {
        $balances = $this->leaveService->getBalances($employeeId);

        return ApiResponse::success($balances, 'Leave balances retrieved successfully.');
    }

    public function process(int $id, LeaveRequestProcessRequest $request): JsonResponse
    {
        try {
            $processedRequest = $this->leaveService->process($id, $request->toDto());

            return ApiResponse::success($processedRequest, 'Leave request processed successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}

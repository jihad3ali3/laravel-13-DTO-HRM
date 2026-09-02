<?php

namespace App\Http\Controllers\Api\V1\Expense;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimFilterRequest;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimProcessRequest;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\ExpenseService;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function index(ExpenseClaimFilterRequest $request): JsonResponse
    {
        $claims = $this->expenseService->getClaims($request->toDto());

        return ApiResponse::success($claims, 'Expense claims retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $claim = $this->expenseService->show($id);

            return ApiResponse::success($claim, 'Expense claim details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(ExpenseClaimStoreRequest $request): JsonResponse
    {
        $claim = $this->expenseService->store($request->toDto());

        return ApiResponse::created($claim, 'Expense claim submitted successfully.');
    }

    public function process(int $id, ExpenseClaimProcessRequest $request): JsonResponse
    {
        try {
            $claim = $this->expenseService->process($id, $request->toDto());

            return ApiResponse::success($claim, 'Expense claim processed successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}

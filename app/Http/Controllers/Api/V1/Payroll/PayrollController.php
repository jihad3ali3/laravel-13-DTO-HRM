<?php

namespace App\Http\Controllers\Api\V1\Payroll;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payroll\PayrollGenerateRequest;
use App\Http\Requests\Api\V1\Payroll\SalaryStructureUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\PayrollService;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    public function __construct(private readonly PayrollService $payrollService) {}

    public function index(): JsonResponse
    {
        $payrolls = $this->payrollService->getPayrolls();

        return ApiResponse::success($payrolls, 'Payroll records retrieved successfully.');
    }

    public function generate(PayrollGenerateRequest $request): JsonResponse
    {
        try {
            $generated = $this->payrollService->generate($request->toDto());

            return ApiResponse::success($generated, 'Payroll batch generated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function payslip(int $id): JsonResponse
    {
        try {
            $payroll = $this->payrollService->show($id);

            return ApiResponse::success($payroll, 'Payslip details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function salaryStructure(int $employeeId): JsonResponse
    {
        $structure = $this->payrollService->salaryStructure($employeeId);

        return ApiResponse::success($structure, 'Salary structure retrieved successfully.');
    }

    public function updateSalaryStructure(SalaryStructureUpdateRequest $request): JsonResponse
    {
        $structure = $this->payrollService->updateSalaryStructure($request->toDto());

        return ApiResponse::success($structure, 'Salary structure updated successfully.');
    }

    public function markAsPaid(int $id): JsonResponse
    {
        try {
            $payroll = $this->payrollService->markAsPaid($id);

            return ApiResponse::success($payroll, 'Payroll marked as paid successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}

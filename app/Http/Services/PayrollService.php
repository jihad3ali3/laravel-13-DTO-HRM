<?php

namespace App\Http\Services;

use App\Domain\Payroll\PayrollGenerateDto;
use App\Domain\Payroll\SalaryStructureUpdateDto;
use App\Repositories\PayrollRepository;

readonly class PayrollService
{
    public function __construct(private PayrollRepository $payrollRepository) {}

    public function getPayrolls()
    {
        return $this->payrollRepository->getPayrolls();
    }

    public function generate(PayrollGenerateDto $dto): array
    {
        return $this->payrollRepository->generate($dto);
    }

    public function show(int $id)
    {
        return $this->payrollRepository->show($id);
    }

    public function salaryStructure(int $employeeId)
    {
        return $this->payrollRepository->salaryStructure($employeeId);
    }

    public function updateSalaryStructure(SalaryStructureUpdateDto $dto)
    {
        return $this->payrollRepository->updateSalaryStructure($dto);
    }

    public function markAsPaid(int $id)
    {
        return $this->payrollRepository->markAsPaid($id);
    }
}

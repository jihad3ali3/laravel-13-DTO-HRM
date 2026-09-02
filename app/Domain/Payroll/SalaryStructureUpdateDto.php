<?php

namespace App\Domain\Payroll;

class SalaryStructureUpdateDto
{
    public function __construct(
        public int $employeeId,
        public float $basicSalary,
        public float $hra,
        public float $medicalAllowance,
        public float $transportAllowance,
        public float $specialAllowance,
        public float $taxDeduction,
        public float $providentFund,
        public float $otherDeductions
    ) {}
}

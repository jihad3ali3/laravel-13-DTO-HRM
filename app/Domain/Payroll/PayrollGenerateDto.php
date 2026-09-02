<?php

namespace App\Domain\Payroll;

class PayrollGenerateDto
{
    public function __construct(
        public int $month,
        public int $year
    ) {}
}

<?php

namespace App\Domain\Expense;

class ExpenseClaimFilterDto
{
    public function __construct(
        public ?int $employeeId = null,
        public ?string $status = null,
        public int $perPage = 15
    ) {}
}

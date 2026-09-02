<?php

namespace App\Domain\Expense;

class ExpenseClaimStoreDto
{
    public function __construct(
        public int $employeeId,
        public string $title,
        public string $category,
        public float $amount,
        public string $expenseDate,
        public ?string $description = null,
        public ?string $receiptFile = null
    ) {}
}

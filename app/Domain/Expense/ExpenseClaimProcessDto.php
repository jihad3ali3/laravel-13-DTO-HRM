<?php

namespace App\Domain\Expense;

class ExpenseClaimProcessDto
{
    public function __construct(
        public string $status,
        public ?string $rejectReason = null,
        public ?int $actionedBy = null
    ) {}
}

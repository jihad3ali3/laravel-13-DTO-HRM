<?php

namespace App\Domain\Leave;

class LeaveRequestProcessDto
{
    public function __construct(
        public string $status,
        public ?string $rejectReason = null,
        public ?int $actionedBy = null
    ) {}
}

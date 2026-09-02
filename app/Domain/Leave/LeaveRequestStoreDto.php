<?php

namespace App\Domain\Leave;

class LeaveRequestStoreDto
{
    public function __construct(
        public int $employeeId,
        public int $leaveId,
        public string $startDate,
        public string $endDate,
        public ?string $reason = null
    ) {}
}

<?php

namespace App\Domain\Leave;

class LeaveFilterDto
{
    public function __construct(
        public ?int $employeeId = null,
        public ?string $status = null,
        public int $perPage = 15
    ) {}
}

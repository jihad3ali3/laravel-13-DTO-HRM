<?php

namespace App\Domain\Shift;

class ShiftAssignDto
{
    public function __construct(
        public int $employeeId,
        public int $shiftId,
        public string $startDate,
        public string $endDate,
        public bool $isActive = true
    ) {}
}

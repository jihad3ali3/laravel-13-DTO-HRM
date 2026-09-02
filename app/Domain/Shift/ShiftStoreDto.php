<?php

namespace App\Domain\Shift;

class ShiftStoreDto
{
    public function __construct(
        public string $name,
        public string $shiftCode,
        public string $startTime,
        public string $endTime,
        public int $markLateAfterMinutes = 0,
        public float $totalHours = 0.0,
        public bool $isActive = true
    ) {}
}

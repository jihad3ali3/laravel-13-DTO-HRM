<?php

namespace App\Domain\Performance;

class PerformanceGoalStoreDto
{
    public function __construct(
        public int $employeeId,
        public string $title,
        public ?string $description,
        public string $startDate,
        public string $dueDate,
        public string $priority
    ) {}
}

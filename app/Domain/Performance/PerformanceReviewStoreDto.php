<?php

namespace App\Domain\Performance;

class PerformanceReviewStoreDto
{
    public function __construct(
        public int $employeeId,
        public int $reviewerId,
        public string $reviewCycle,
        public int $rating,
        public ?string $strengths,
        public ?string $areaOfImprovements,
        public ?string $employeeFeedback = null,
        public string $status = 'draft'
    ) {}
}

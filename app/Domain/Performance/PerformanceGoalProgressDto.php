<?php

namespace App\Domain\Performance;

class PerformanceGoalProgressDto
{
    public function __construct(
        public int $progress
    ) {}
}

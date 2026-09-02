<?php

namespace App\Http\Services;

use App\Repositories\DashboardRepository;

readonly class DashboardService
{
    public function __construct(private DashboardRepository $dashboardRepository) {}

    public function getMetrics(): array
    {
        return $this->dashboardRepository->getMetrics();
    }
}

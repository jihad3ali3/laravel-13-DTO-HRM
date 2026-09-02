<?php

namespace App\Http\Services;

use App\Repositories\ReportRepository;

readonly class ReportService
{
    public function __construct(private ReportRepository $reportRepository) {}

    public function attendance(?string $startDate = null, ?string $endDate = null): array
    {
        return $this->reportRepository->attendance($startDate, $endDate);
    }

    public function leaves(): array
    {
        return $this->reportRepository->leaves();
    }

    public function payroll(): array
    {
        return $this->reportRepository->payroll();
    }

    public function headcount(): array
    {
        return $this->reportRepository->headcount();
    }
}

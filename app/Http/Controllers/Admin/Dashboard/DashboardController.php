<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index()
    {
        $metrics = $this->dashboardService->getMetrics();

        return view('admin.dashboard.index', compact('metrics'));
    }
}

<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(): View
    {
        $headcount = $this->reportService->headcount();
        $leaves = $this->reportService->leaves();
        $payroll = $this->reportService->payroll();

        return view('admin.reports.index', compact('headcount', 'leaves', 'payroll'));
    }

    public function headcount(): View
    {
        $data = $this->reportService->headcount();

        return view('admin.reports.headcount', compact('data'));
    }

    public function attendance(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $data = $this->reportService->attendance($startDate, $endDate);

        return view('admin.reports.attendance', compact('data', 'startDate', 'endDate'));
    }

    public function leaves(): View
    {
        $data = $this->reportService->leaves();

        return view('admin.reports.leaves', compact('data'));
    }

    public function payroll(): View
    {
        $data = $this->reportService->payroll();

        return view('admin.reports.payroll', compact('data'));
    }
}

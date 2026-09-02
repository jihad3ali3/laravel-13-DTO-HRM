@extends('layouts.admin-layout')

@section('title', 'Reports & Analytics')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">HR Analytics &amp; Reports Central</h4>
            <p class="mb-0 text-muted">Comprehensive analytical reporting on headcount, attendance trends, leave utilization, and payroll disbursements</p>
        </div>
    </div>

    <!-- Analytics Hub Cards -->
    <div class="row g-6 mb-6">
        <!-- 1. Headcount Analytics -->
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 card-border-shadow-primary">
                <div class="card-body">
                    <div class="avatar avatar-md mb-3"><span class="avatar-initial rounded bg-label-primary fs-4"><i class="icon-base ti tabler-users"></i></span></div>
                    <h5 class="card-title fw-bold mb-1">Headcount Analytics</h5>
                    <p class="text-muted small mb-4">Total active personnel, department distribution, and employment types.</p>
                    <a href="{{ route('admin::reports.headcount') }}" class="btn btn-sm btn-primary w-100">
                        <i class="icon-base ti tabler-chart-pie me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Attendance Reports -->
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 card-border-shadow-success">
                <div class="card-body">
                    <div class="avatar avatar-md mb-3"><span class="avatar-initial rounded bg-label-success fs-4"><i class="icon-base ti tabler-calendar-time"></i></span></div>
                    <h5 class="card-title fw-bold mb-1">Attendance Reports</h5>
                    <p class="text-muted small mb-4">Monthly attendance turnout rates, late arrivals, and total logged working hours.</p>
                    <a href="{{ route('admin::reports.attendance') }}" class="btn btn-sm btn-success w-100">
                        <i class="icon-base ti tabler-chart-bar me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Leave Utilization -->
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 card-border-shadow-warning">
                <div class="card-body">
                    <div class="avatar avatar-md mb-3"><span class="avatar-initial rounded bg-label-warning fs-4"><i class="icon-base ti tabler-calendar-due"></i></span></div>
                    <h5 class="card-title fw-bold mb-1">Leave Utilization</h5>
                    <p class="text-muted small mb-4">Leave approval statistics, categorized policy utilization, and absence metrics.</p>
                    <a href="{{ route('admin::reports.leaves') }}" class="btn btn-sm btn-warning w-100">
                        <i class="icon-base ti tabler-chart-donut me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Payroll Analytics -->
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 card-border-shadow-info">
                <div class="card-body">
                    <div class="avatar avatar-md mb-3"><span class="avatar-initial rounded bg-label-info fs-4"><i class="icon-base ti tabler-file-dollar"></i></span></div>
                    <h5 class="card-title fw-bold mb-1">Payroll Analytics</h5>
                    <p class="text-muted small mb-4">Monthly salary disbursement trends, net payouts, allowances, and tax deductions.</p>
                    <a href="{{ route('admin::reports.payroll') }}" class="btn btn-sm btn-info w-100">
                        <i class="icon-base ti tabler-report-money me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

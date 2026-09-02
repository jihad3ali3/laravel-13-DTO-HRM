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

    <!-- Corporate Data Export Center -->
    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-1"><i class="icon-base ti tabler-download me-2 text-primary"></i>Corporate Data Export Center</h5>
                    <small class="text-muted">Instant Excel-compatible CSV downloads and printable audit master sheets</small>
                </div>
                <span class="badge bg-label-primary">Excel &bull; CSV &bull; PDF</span>
            </div>
        </div>
        <div class="card-body pt-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-users icon-20px text-primary me-2"></i>
                                <h6 class="mb-0 fw-bold">Employee Directory</h6>
                            </div>
                            <p class="text-muted small mb-3">All personnel records, departments, designations, contacts, and statuses.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin::export.employees.csv') }}" class="btn btn-sm btn-label-success flex-grow-1">
                                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> CSV
                            </a>
                            <a href="{{ route('admin::export.employees.print') }}" target="_blank" class="btn btn-sm btn-label-secondary">
                                <i class="icon-base ti tabler-printer me-1"></i> Print Roll
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-clock icon-20px text-success me-2"></i>
                                <h6 class="mb-0 fw-bold">Attendance Timesheets</h6>
                            </div>
                            <p class="text-muted small mb-3">Complete time tracking log, check-in/out timestamps, and working hours.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin::export.attendance.csv') }}" class="btn btn-sm btn-label-success w-100">
                                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Download Timesheet CSV
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-file-dollar icon-20px text-info me-2"></i>
                                <h6 class="mb-0 fw-bold">Payroll Disbursements</h6>
                            </div>
                            <p class="text-muted small mb-3">Detailed monthly salary registers, gross wages, allowances, and tax deductions.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin::export.payroll.csv') }}" class="btn btn-sm btn-label-success flex-grow-1">
                                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> CSV
                            </a>
                            <a href="{{ route('admin::export.payroll.print') }}" target="_blank" class="btn btn-sm btn-label-secondary">
                                <i class="icon-base ti tabler-printer me-1"></i> Print Sheet
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-calendar-due icon-20px text-warning me-2"></i>
                                <h6 class="mb-0 fw-bold">Leave Requests &amp; Entitlements</h6>
                            </div>
                            <p class="text-muted small mb-3">All leave applications, approval history, reason descriptions, and day counts.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin::export.leaves.csv') }}" class="btn btn-sm btn-label-success w-100">
                                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Download Leaves CSV
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border rounded h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="icon-base ti tabler-receipt icon-20px text-danger me-2"></i>
                                <h6 class="mb-0 fw-bold">Expense Reimbursements</h6>
                            </div>
                            <p class="text-muted small mb-3">Reimbursement claim codes, categories, claims amounts, and tax invoices.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin::export.expenses.csv') }}" class="btn btn-sm btn-label-success w-100">
                                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Download Claims CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

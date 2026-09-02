@extends('layouts.admin-layout')

@section('title', 'HRM Dashboard')

@section('container')
    <!-- Welcome Header -->
    <div class="row mb-6">
        <div class="col-12">
            <div class="card bg-label-primary border-0 shadow-none">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
                    <div class="mb-3 mb-md-0">
                        <h4 class="mb-1 text-primary">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h4>
                        <p class="mb-0 text-body">Here is what is happening across your workforce today, {{ \Carbon\Carbon::now()->format('l, d F Y') }}.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @can('employees.create')
                            @if(Route::has('admin::employees.create'))
                                <a href="{{ route('admin::employees.create') }}" class="btn btn-primary btn-sm">
                                    <i class="icon-base ti tabler-user-plus me-1"></i> Add Employee
                                </a>
                            @endif
                        @endcan
                        @can('leaves.create')
                            @if(Route::has('admin::leaves.requests'))
                                <a href="{{ route('admin::leaves.requests') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="icon-base ti tabler-calendar-plus me-1"></i> Apply Leave
                                </a>
                            @endif
                        @endcan
                        @can('expenses.create')
                            @if(Route::has('admin::expenses.index'))
                                <a href="{{ route('admin::expenses.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="icon-base ti tabler-receipt-2 me-1"></i> Claim Expense
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Top KPI Summary Cards -->
    <div class="row g-6 mb-6">
        <!-- Card 1: Total Employees -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-users icon-28px"></i>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold">{{ $metrics['total_employees'] ?? 0 }}</h3>
                    </div>
                    <p class="mb-1 fw-medium text-heading">Total Employees</p>
                    <p class="mb-0">
                        <span class="badge bg-label-primary">{{ $metrics['total_departments'] ?? 0 }} Departments</span>
                        <small class="text-body-secondary ms-1">Active Workforce</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Present Today -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-calendar-time icon-28px"></i>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold text-success">{{ $metrics['present_today'] ?? 0 }}</h3>
                    </div>
                    <p class="mb-1 fw-medium text-heading">Present Today</p>
                    <p class="mb-0">
                        <span class="badge bg-label-success">{{ $metrics['attendance_rate'] ?? 0 }}% Rate</span>
                        <small class="text-body-secondary ms-1">{{ $metrics['late_today'] ?? 0 }} Late | {{ $metrics['half_day_today'] ?? 0 }} Half Day</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending Approvals -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-clock-hour-4 icon-28px"></i>
                            </span>
                        </div>
                        <h3 class="mb-0 fw-bold text-warning">{{ $metrics['total_pending_actions'] ?? 0 }}</h3>
                    </div>
                    <p class="mb-1 fw-medium text-heading">Pending Approvals</p>
                    <p class="mb-0">
                        <span class="badge bg-label-warning">{{ $metrics['pending_leaves_count'] ?? 0 }} Leaves</span>
                        <span class="badge bg-label-info ms-1">{{ $metrics['pending_expenses_count'] ?? 0 }} Expenses</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 4: Monthly Payroll -->
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-wallet icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0 fw-bold text-info">₹{{ number_format($metrics['monthly_payroll_disbursed'] ?? 0, 0) }}</h4>
                    </div>
                    <p class="mb-1 fw-medium text-heading">Payroll Disbursed</p>
                    <p class="mb-0">
                        <span class="badge bg-label-info">{{ \Carbon\Carbon::now()->format('F Y') }}</span>
                        <small class="text-body-secondary ms-1">Disbursed Payouts</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Workforce Attendance & Department Breakdown -->
    <div class="row g-6 mb-6">
        <!-- Attendance Status Overview -->
        <div class="col-xxl-6 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Today's Attendance Status</h5>
                        <small class="text-body-secondary">Real-time attendance &amp; leaves breakdown</small>
                    </div>
                    <span class="badge bg-label-primary fs-6">{{ $metrics['attendance_rate'] ?? 0 }}% Turnout</span>
                </div>
                <div class="card-body">
                    <!-- Progress Bar Breakdown -->
                    @php
                        $total = max(1, $metrics['total_employees'] ?? 1);
                        $presPct = round((($metrics['present_count'] ?? 0) / $total) * 100, 1);
                        $latePct = round((($metrics['late_today'] ?? 0) / $total) * 100, 1);
                        $halfPct = round((($metrics['half_day_today'] ?? 0) / $total) * 100, 1);
                        $leavePct = round((($metrics['on_leave_today'] ?? 0) / $total) * 100, 1);
                        $absentPct = round((($metrics['absent_today'] ?? 0) / $total) * 100, 1);
                    @endphp
                    <div class="progress rounded-3 mb-4 bg-transparent overflow-hidden" style="height: 38px;">
                        <div class="progress-bar bg-success fw-medium text-start px-3" style="width: {{ max(8, $presPct) }}%" title="Present">
                            {{ $metrics['present_count'] ?? 0 }} Present
                        </div>
                        @if(($metrics['late_today'] ?? 0) > 0)
                            <div class="progress-bar bg-warning fw-medium text-start px-2" style="width: {{ max(8, $latePct) }}%" title="Late">
                                {{ $metrics['late_today'] ?? 0 }} Late
                            </div>
                        @endif
                        @if(($metrics['half_day_today'] ?? 0) > 0)
                            <div class="progress-bar bg-info fw-medium text-start px-2" style="width: {{ max(8, $halfPct) }}%" title="Half Day">
                                {{ $metrics['half_day_today'] ?? 0 }} Half Day
                            </div>
                        @endif
                        @if(($metrics['on_leave_today'] ?? 0) > 0)
                            <div class="progress-bar bg-primary fw-medium text-start px-2" style="width: {{ max(8, $leavePct) }}%" title="On Leave">
                                {{ $metrics['on_leave_today'] ?? 0 }} Leave
                            </div>
                        @endif
                        @if(($metrics['absent_today'] ?? 0) > 0)
                            <div class="progress-bar bg-danger fw-medium text-start px-2" style="width: {{ max(8, $absentPct) }}%" title="Absent">
                                {{ $metrics['absent_today'] ?? 0 }} Absent
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table card-table table-border-top-0 table-border-bottom-0">
                            <tbody>
                                <tr>
                                    <td class="ps-0">
                                        <span class="badge badge-dot bg-success me-2"></span> Present On Time
                                    </td>
                                    <td class="text-end fw-bold">{{ $metrics['present_count'] ?? 0 }}</td>
                                    <td class="text-end pe-0 text-muted">{{ $presPct }}%</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">
                                        <span class="badge badge-dot bg-warning me-2"></span> Late Check-ins
                                    </td>
                                    <td class="text-end fw-bold text-warning">{{ $metrics['late_today'] ?? 0 }}</td>
                                    <td class="text-end pe-0 text-muted">{{ $latePct }}%</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">
                                        <span class="badge badge-dot bg-info me-2"></span> Half Day
                                    </td>
                                    <td class="text-end fw-bold text-info">{{ $metrics['half_day_today'] ?? 0 }}</td>
                                    <td class="text-end pe-0 text-muted">{{ $halfPct }}%</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">
                                        <span class="badge badge-dot bg-primary me-2"></span> Approved Leaves
                                    </td>
                                    <td class="text-end fw-bold text-primary">{{ $metrics['on_leave_today'] ?? 0 }}</td>
                                    <td class="text-end pe-0 text-muted">{{ $leavePct }}%</td>
                                </tr>
                                <tr>
                                    <td class="ps-0">
                                        <span class="badge badge-dot bg-danger me-2"></span> Absent / Unrecorded
                                    </td>
                                    <td class="text-end fw-bold text-danger">{{ $metrics['absent_today'] ?? 0 }}</td>
                                    <td class="text-end pe-0 text-muted">{{ $absentPct }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="col-xxl-6 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Department Distribution</h5>
                        <small class="text-body-secondary">Headcount by organizational division</small>
                    </div>
                    @if(Route::has('admin::settings.departments'))
                        <a href="{{ route('admin::settings.departments') }}" class="btn btn-sm btn-label-secondary">Manage</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(!empty($metrics['department_distribution']) && count($metrics['department_distribution']) > 0)
                        <div class="d-flex flex-column gap-4">
                            @foreach($metrics['department_distribution'] as $dept)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-medium text-heading">
                                            <span class="badge bg-label-primary me-1">{{ $dept['code'] }}</span>
                                            {{ $dept['name'] }}
                                        </span>
                                        <div>
                                            <span class="fw-bold">{{ $dept['count'] }}</span>
                                            <small class="text-muted">({{ $dept['percentage'] }}%)</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $dept['percentage'] }}%" aria-valuenow="{{ $dept['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="icon-base ti tabler-building icon-48px text-muted mb-2"></i>
                            <p class="text-muted mb-0">No active departments found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Recent Pending Approvals (Leaves & Expenses) -->
    <div class="row g-6 mb-6">
        <!-- Recent Leave Requests -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Recent Leave Requests</h5>
                    @if(Route::has('admin::leaves.requests'))
                        <a href="{{ route('admin::leaves.requests') }}" class="btn btn-sm btn-label-primary">View All</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Dates</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if(!empty($metrics['recent_leave_requests']) && count($metrics['recent_leave_requests']) > 0)
                                @foreach($metrics['recent_leave_requests'] as $req)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-info">
                                                        {{ strtoupper(substr($req->employee->first_name ?? 'E', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 small">{{ $req->employee->first_name ?? 'Employee' }} {{ $req->employee->last_name ?? '' }}</h6>
                                                    <small class="text-muted">{{ $req->employee->department->name ?? 'General' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $req->leave->name ?? 'Leave' }}</span>
                                        </td>
                                        <td>
                                            <small class="d-block">{{ \Carbon\Carbon::parse($req->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            @if($req->status === 'approved')
                                                <span class="badge bg-label-success">Approved</span>
                                            @elseif($req->status === 'rejected')
                                                <span class="badge bg-label-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No leave requests found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Expense Claims -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Recent Expense Claims</h5>
                    @if(Route::has('admin::expenses.index'))
                        <a href="{{ route('admin::expenses.index') }}" class="btn btn-sm btn-label-primary">View All</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Title / Category</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if(!empty($metrics['recent_expense_claims']) && count($metrics['recent_expense_claims']) > 0)
                                @foreach($metrics['recent_expense_claims'] as $claim)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                                        {{ strtoupper(substr($claim->employee->first_name ?? 'E', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 small">{{ $claim->employee->first_name ?? 'Employee' }} {{ $claim->employee->last_name ?? '' }}</h6>
                                                    <small class="text-muted">{{ $claim->claim_code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block small fw-medium">{{ $claim->title }}</span>
                                            <span class="badge bg-label-secondary">{{ ucfirst($claim->category) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">₹{{ number_format($claim->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($claim->status === 'reimbursed')
                                                <span class="badge bg-label-success">Reimbursed</span>
                                            @elseif($claim->status === 'accepted')
                                                <span class="badge bg-label-info">Accepted</span>
                                            @elseif($claim->status === 'rejected')
                                                <span class="badge bg-label-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No expense claims found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Notices & Holidays Schedule -->
    <div class="row g-6 mb-6">
        <!-- Notice Board / Announcements -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="icon-base ti tabler-speakerphone icon-24px me-2 text-primary"></i>
                        <h5 class="card-title mb-0">Company Notice Board</h5>
                    </div>
                    @if(Route::has('admin::announcements.index'))
                        <a href="{{ route('admin::announcements.index') }}" class="btn btn-sm btn-label-secondary">All Notices</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(!empty($metrics['recent_announcements']) && count($metrics['recent_announcements']) > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($metrics['recent_announcements'] as $announcement)
                                <div class="p-3 rounded border border-light">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center">
                                            @if($announcement->is_pinned)
                                                <span class="badge bg-label-danger me-2"><i class="icon-base ti tabler-pinned me-1"></i> Pinned</span>
                                            @endif
                                            <h6 class="mb-0 fw-bold">{{ $announcement->title }}</h6>
                                        </div>
                                        <span class="badge bg-label-{{ $announcement->priority === 'high' ? 'danger' : ($announcement->priority === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($announcement->priority) }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($announcement->content, 120) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="icon-base ti tabler-calendar me-1"></i> {{ \Carbon\Carbon::parse($announcement->start_date)->format('M d, Y') }}
                                        </small>
                                        @if($announcement->department)
                                            <span class="badge bg-label-secondary">{{ $announcement->department->name }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">All Company</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="icon-base ti tabler-speakerphone-off icon-48px text-muted mb-2"></i>
                            <p class="text-muted mb-0">No active announcements posted.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="icon-base ti tabler-calendar-heart icon-24px me-2 text-danger"></i>
                        <h5 class="card-title mb-0">Upcoming Holidays</h5>
                    </div>
                    @if(Route::has('admin::holidays.index'))
                        <a href="{{ route('admin::holidays.index') }}" class="btn btn-sm btn-label-secondary">Calendar</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(!empty($metrics['upcoming_holidays']) && count($metrics['upcoming_holidays']) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($metrics['upcoming_holidays'] as $holiday)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class="icon-base ti tabler-calendar-event"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $holiday->name }}</h6>
                                            <small class="text-muted">{{ $holiday->description ?? 'Holiday Celebration' }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-label-primary mb-1 d-block">{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('D, M d, Y') }}</span>
                                        <small class="badge bg-label-secondary text-capitalize">{{ $holiday->type }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="icon-base ti tabler-calendar-off icon-48px text-muted mb-2"></i>
                            <p class="text-muted mb-0">No upcoming holidays scheduled.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Today's Real-time Check-in Log Table -->
    <div class="row g-6">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Today's Check-in Log</h5>
                        <small class="text-body-secondary">Real-time daily roster entries for {{ \Carbon\Carbon::now()->format('d M Y') }}</small>
                    </div>
                    @if(Route::has('admin::attendance.index'))
                        <a href="{{ route('admin::attendance.index') }}" class="btn btn-sm btn-label-primary">View Full Attendance</a>
                    @endif
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if(!empty($metrics['today_attendances']) && count($metrics['today_attendances']) > 0)
                                @foreach($metrics['today_attendances'] as $att)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 small">{{ $att->employee->first_name ?? 'Employee' }} {{ $att->employee->last_name ?? '' }}</h6>
                                                    <small class="text-muted">{{ $att->employee->employee_code ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $att->employee->department->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-heading"><i class="icon-base ti tabler-login me-1 text-success"></i>{{ $att->check_in ?? '--:--' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted"><i class="icon-base ti tabler-logout me-1 text-danger"></i>{{ $att->check_out ?? '--:--' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $att->working_hours ?? '0.00' }} hrs</span>
                                        </td>
                                        <td>
                                            @if($att->status === 'present')
                                                <span class="badge bg-label-success">Present</span>
                                            @elseif($att->status === 'late')
                                                <span class="badge bg-label-warning">Late</span>
                                            @elseif($att->status === 'half_day')
                                                <span class="badge bg-label-info">Half Day</span>
                                            @else
                                                <span class="badge bg-label-danger">Absent</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="icon-base ti tabler-clock-off icon-32px d-block mb-1"></i>
                                        No attendance check-ins recorded yet for today.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

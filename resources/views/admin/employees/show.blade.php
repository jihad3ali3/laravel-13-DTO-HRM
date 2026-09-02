@extends('layouts.admin-layout')

@section('title', $employee->first_name . ' ' . $employee->last_name . ' - Profile')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
            <p class="mb-0 text-muted">Employee Code: <span class="badge bg-label-primary">{{ $employee->employee_code }}</span> | Role: <span class="badge bg-label-info">{{ $employee->user->roles->first()->name ?? 'Employee' }}</span></p>
        </div>
        <div class="d-flex gap-2">
            @can('employees.edit')
                <a href="{{ route('admin::employees.edit', $employee->id) }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-pencil me-1"></i> Edit Profile
                </a>
            @endcan
            <a href="{{ route('admin::employees.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Header Card -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="avatar avatar-xl">
                    <span class="avatar-initial rounded-circle bg-label-primary fs-2">
                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-grow-1 text-center text-md-start">
                    <h5 class="mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 text-muted small">
                        <span><i class="icon-base ti tabler-briefcase me-1"></i> {{ $employee->designation->title ?? 'General' }}</span>
                        <span><i class="icon-base ti tabler-building me-1"></i> {{ $employee->department->name ?? 'Unassigned' }}</span>
                        <span><i class="icon-base ti tabler-mail me-1"></i> {{ $employee->email }}</span>
                        <span><i class="icon-base ti tabler-phone me-1"></i> {{ $employee->phone ?? 'N/A' }}</span>
                        <span><i class="icon-base ti tabler-calendar me-1"></i> Joined: {{ \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') }}</span>
                    </div>
                </div>
                <div>
                    @if($employee->is_active)
                        <span class="badge bg-label-success fs-6"><i class="icon-base ti tabler-circle-check me-1"></i> Active</span>
                    @else
                        <span class="badge bg-label-danger fs-6"><i class="icon-base ti tabler-circle-x me-1"></i> Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Detail Tabs -->
    <div class="nav-align-top mb-6">
        <ul class="nav nav-pills mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
                    <i class="icon-base ti tabler-id me-1"></i> Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-attendance" type="button" role="tab">
                    <i class="icon-base ti tabler-calendar-time me-1"></i> Recent Attendance
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leaves" type="button" role="tab">
                    <i class="icon-base ti tabler-calendar-due me-1"></i> Leave History
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents" type="button" role="tab">
                    <i class="icon-base ti tabler-file-text me-1"></i> Documents ({{ $employee->documents->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-salary" type="button" role="tab">
                    <i class="icon-base ti tabler-file-dollar me-1"></i> Salary Structure
                </button>
            </li>
        </ul>

        <div class="tab-content bg-transparent border-0 p-0">
            <!-- 1. Overview Tab -->
            <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                <div class="row g-6">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><h5 class="card-title mb-0">Personal Information</h5></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Full Name</span>
                                        <span class="fw-medium">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Gender</span>
                                        <span class="text-capitalize">{{ $employee->gender }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Date of Birth</span>
                                        <span>{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d M, Y') : 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Address</span>
                                        <span class="text-end">{{ $employee->address ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Emergency Contact</span>
                                        <span class="text-end">{{ $employee->emergency_contact ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><h5 class="card-title mb-0">Employment &amp; Organization</h5></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Department</span>
                                        <span class="fw-medium">{{ $employee->department->name ?? 'Unassigned' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Designation</span>
                                        <span class="fw-medium">{{ $employee->designation->title ?? 'General' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Employment Type</span>
                                        <span class="badge bg-label-primary text-capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Current Shift</span>
                                        <span>{{ $employee->employeeShifts->first()->shift->name ?? 'Standard Day Shift' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Basic Monthly Salary</span>
                                        <span class="fw-bold text-success">₹{{ number_format($employee->salary, 2) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Attendance Tab -->
            <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Working Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->attendances as $att)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($att->attendance_date)->format('D, d M Y') }}</td>
                                        <td><span class="text-success">{{ $att->check_in ?? '--:--' }}</span></td>
                                        <td><span class="text-danger">{{ $att->check_out ?? '--:--' }}</span></td>
                                        <td>{{ $att->working_hours ?? '0.00' }} hrs</td>
                                        <td>
                                            <span class="badge bg-label-{{ $att->status === 'present' ? 'success' : ($att->status === 'late' ? 'warning' : 'info') }}">
                                                {{ ucfirst($att->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No attendance logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Leaves Tab -->
            <div class="tab-pane fade" id="tab-leaves" role="tabpanel">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Duration</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaveRequests as $req)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($req->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}</td>
                                        <td>{{ $req->reason ?? 'Personal' }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $req->status === 'approved' ? 'success' : ($req->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No leave requests found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Documents Tab -->
            <div class="tab-pane fade" id="tab-documents" role="tabpanel">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Document Number</th>
                                    <th>Verification Status</th>
                                    <th>Uploaded Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->documents as $doc)
                                    <tr>
                                        <td><span class="badge bg-label-secondary text-capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</span></td>
                                        <td>{{ $doc->document_number ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $doc->verification_status === 'verified' ? 'success' : ($doc->verification_status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($doc->verification_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No documents uploaded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. Salary Tab -->
            <div class="tab-pane fade" id="tab-salary" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        @if($employee->salaryStructure)
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="p-3 bg-label-primary rounded">
                                        <small class="d-block text-muted">Basic Salary</small>
                                        <h5 class="mb-0 fw-bold">₹{{ number_format($employee->salaryStructure->basic_salary, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-label-info rounded">
                                        <small class="d-block text-muted">HRA</small>
                                        <h5 class="mb-0 fw-bold">₹{{ number_format($employee->salaryStructure->hra, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-label-danger rounded">
                                        <small class="d-block text-muted">Provident Fund (PF)</small>
                                        <h5 class="mb-0 fw-bold">₹{{ number_format($employee->salaryStructure->provident_fund, 2) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 bg-label-success rounded">
                                        <small class="d-block text-muted">Net Monthly Pay</small>
                                        <h5 class="mb-0 fw-bold text-success">₹{{ number_format($employee->salaryStructure->net_salary, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                Basic salary assigned: <strong>₹{{ number_format($employee->salary, 2) }}</strong> (Detailed breakdown structure not yet initialized).
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

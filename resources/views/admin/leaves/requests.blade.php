@extends('layouts.admin-layout')

@section('title', 'Leave Requests')

@section('container')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h4 class="mb-1">Leave Applications &amp; Approvals</h4>
            <p class="mb-0 text-muted">Review leave requests, approve/reject applications, and monitor employee balances</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin::export.leaves.csv', request()->query()) }}" class="btn btn-label-success">
                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Export Leaves
            </a>
            @if(auth()->user()->can('leaves.create') || auth()->user()->employee)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                    <i class="icon-base ti tabler-calendar-plus me-1"></i> Apply for Leave
                </button>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $pendingCount }}</h4>
                        <small class="text-muted">Pending Approvals</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-clock-hour-4"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $approvedCount }}</h4>
                        <small class="text-muted">Approved Leaves</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-danger">{{ $rejectedCount }}</h4>
                        <small class="text-muted">Rejected Requests</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-x"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::leaves.requests') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Leave Type</label>
                    <select name="leave_id" class="form-select">
                        <option value="">All Types</option>
                        @foreach($leaveTypes as $lt)
                            <option value="{{ $lt->id }}" {{ request('leave_id') == $lt->id ? 'selected' : '' }}>
                                {{ $lt->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::leaves.requests') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leaveRequests as $req)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($req->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $req->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $req->employee->first_name ?? 'Employee' }} {{ $req->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $req->employee->department->name ?? 'General' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $req->leave->name ?? 'Leave' }}</span>
                            </td>
                            <td>
                                <span class="d-block text-heading small fw-medium">
                                    {{ \Carbon\Carbon::parse($req->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($req->end_date)->format('M d, Y') }}
                                </span>
                                @php
                                    $days = \Carbon\Carbon::parse($req->start_date)->diffInDays(\Carbon\Carbon::parse($req->end_date)) + 1;
                                @endphp
                                <small class="text-muted">{{ $days }} {{ \Illuminate\Support\Str::plural('day', $days) }}</small>
                            </td>
                            <td>
                                <span class="small text-muted">{{ \Illuminate\Support\Str::limit($req->reason ?? 'Personal', 30) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $req->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge bg-label-success">Approved</span>
                                @elseif($req->status === 'rejected')
                                    <span class="badge bg-label-danger" title="{{ $req->reject_reason }}">Rejected</span>
                                @else
                                    <span class="badge bg-label-warning">Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('leaves.approve')
                                    @if($req->status === 'pending')
                                        <div class="d-flex justify-content-end gap-1">
                                            <form method="POST" action="{{ route('admin::leaves.process', $req->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-success" title="Approve Leave">
                                                    <i class="icon-base ti tabler-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}" title="Reject Leave">
                                                <i class="icon-base ti tabler-x"></i>
                                            </button>
                                        </div>

                                        <!-- Reject Reason Modal -->
                                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm text-start">
                                                <form method="POST" action="{{ route('admin::leaves.process', $req->id) }}" class="modal-content">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Leave</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Rejection Reason</label>
                                                        <textarea name="reject_reason" rows="3" class="form-control" placeholder="Specify reason..." required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Actioned</span>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaveRequests->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>

    <!-- Apply Leave Modal -->
    <div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::leaves.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(auth()->user()->can('leaves.manage') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
                        <div class="mb-3">
                            <label class="form-label required">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (auth()->user()->employee?->id === $emp->id) ? 'selected' : '' }}>
                                        {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                        <div class="alert alert-primary py-2 px-3 mb-3">
                            <small><i class="icon-base ti tabler-user me-1"></i> Submitting application for: <strong>{{ auth()->user()->name }}</strong></small>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label required">Leave Type</label>
                        <select name="leave_id" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }} (Max {{ $lt->days_allowed }} days/yr)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Reason for Leave</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Provide detailed explanation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-send me-1"></i> Submit Application</button>
                </div>
            </form>
        </div>
    </div>
@endsection

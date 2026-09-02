@extends('layouts.admin-layout')

@section('title', 'Attendance Management')

@section('container')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h4 class="mb-1">Attendance Management</h4>
            <p class="mb-0 text-muted">Track daily rosters, time-logs, late check-ins, and working hours</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin::export.attendance.csv', request()->query()) }}" class="btn btn-label-success">
                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Export Timesheet
            </a>
            @can('attendance.manage')
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkInModal">
                    <i class="icon-base ti tabler-login me-1"></i> Quick Check In
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#checkOutModal">
                    <i class="icon-base ti tabler-logout me-1"></i> Quick Check Out
                </button>
            @endcan
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $presentCount }}</h4>
                        <small class="text-muted">Present On Time</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-user-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $lateCount }}</h4>
                        <small class="text-muted">Late Check-ins</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-clock-exclamation"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-info">{{ $halfDayCount }}</h4>
                        <small class="text-muted">Half Day Entries</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-hourglass"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-danger">{{ $absentCount }}</h4>
                        <small class="text-muted">Recorded Absentees</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-user-x"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::attendance.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Attendance Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                        <option value="half_day" {{ request('status') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                        <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::attendance.index') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card">
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
                    @forelse($attendances as $att)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($att->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $att->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $att->employee->first_name ?? 'Employee' }} {{ $att->employee->last_name ?? '' }}
                                        </a>
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
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="icon-base ti tabler-clock-off icon-48px text-muted mb-2"></i>
                                <h6 class="mb-0 text-muted">No attendance records found for {{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Check In Modal -->
    <div class="modal fade" id="checkInModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::attendance.checkIn') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Check-In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Check-In Time</label>
                        <input type="time" name="check_in" class="form-control" value="{{ date('H:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes (e.g. On-site client visit)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="icon-base ti tabler-login me-1"></i> Check In</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Check Out Modal -->
    <div class="modal fade" id="checkOutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::attendance.checkOut') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Check-Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Check-Out Time</label>
                        <input type="time" name="check_out" class="form-control" value="{{ date('H:i') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="icon-base ti tabler-logout me-1"></i> Check Out</button>
                </div>
            </form>
        </div>
    </div>
@endsection

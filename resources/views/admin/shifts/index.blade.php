@extends('layouts.admin-layout')

@section('title', 'Shift Management & Scheduling')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Shift Management &amp; Rostering</h4>
            <p class="mb-0 text-muted">Configure work shifts, timings, grace periods, and allocate shifts to staff</p>
        </div>
        @can('shifts.manage')
            <div class="d-flex gap-2">
                <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#assignShiftModal">
                    <i class="icon-base ti tabler-user-share me-1"></i> Assign Shift
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createShiftModal">
                    <i class="icon-base ti tabler-clock-plus me-1"></i> Add Shift Schedule
                </button>
            </div>
        @endcan
    </div>

    <!-- Shifts Cards -->
    <div class="row g-4 mb-6">
        @forelse($shifts as $shift)
            <div class="col-md-4">
                <div class="card h-100 card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-label-primary fs-6">{{ $shift->name }}</span>
                            <span class="badge bg-label-info">{{ $shift->code }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar avatar-md"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-clock"></i></span></div>
                            <div>
                                <h5 class="mb-0">{{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }}</h5>
                                <small class="text-muted">Total: {{ $shift->total_hours ?? '9.00' }} hrs/day</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small border-top pt-2">
                            <span>Grace Time: {{ $shift->grace_time_minutes ?? 15 }} mins</span>
                            <span>{{ $shift->description ?? 'Standard Shift' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    No shifts defined yet. Click "Add Shift Schedule" to create one.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Active Shift Rosters Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Active Employee Shift Assignments</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Assigned Shift</th>
                        <th>Timings</th>
                        <th>Effective Period</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($employeeShifts as $es)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($es->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 small">{{ $es->employee->first_name ?? 'Employee' }} {{ $es->employee->last_name ?? '' }}</h6>
                                        <small class="text-muted">{{ $es->employee->employee_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $es->employee->department->name ?? 'General' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $es->shift->name ?? 'General Shift' }}</span>
                            </td>
                            <td>
                                <span class="small text-heading">{{ substr($es->shift->start_time ?? '09:00', 0, 5) }} - {{ substr($es->shift->end_time ?? '18:00', 0, 5) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $es->start_date ? \Carbon\Carbon::parse($es->start_date)->format('M d, Y') : 'Immediate' }} - {{ $es->end_date ? \Carbon\Carbon::parse($es->end_date)->format('M d, Y') : 'Ongoing' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No individual shift overrides assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employeeShifts->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $employeeShifts->links() }}
            </div>
        @endif
    </div>

    <!-- Create Shift Modal -->
    <div class="modal fade" id="createShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::shifts.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Shift Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Shift Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Morning Regular Shift" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Shift Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. S-DAY" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">End Time</label>
                            <input type="time" name="end_time" class="form-control" value="18:00" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Grace Minutes</label>
                            <input type="number" name="grace_time_minutes" class="form-control" value="15">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Total Hours</label>
                            <input type="number" step="0.5" name="total_hours" class="form-control" value="9.0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Create Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Shift Modal -->
    <div class="modal fade" id="assignShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::shifts.assign') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Shift to Employee</h5>
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
                        <label class="form-label required">Shift</label>
                        <select name="shift_id" class="form-select" required>
                            <option value="">Select Shift</option>
                            @foreach($shifts as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label required">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Assign Shift</button>
                </div>
            </form>
        </div>
    </div>
@endsection

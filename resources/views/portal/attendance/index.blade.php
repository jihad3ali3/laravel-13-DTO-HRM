@extends('layouts.admin-layout')

@section('title', 'My Attendance & Timesheets')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">My Attendance &amp; Timesheets</h4>
            <p class="mb-0 text-muted">Assigned Shift: <span class="badge bg-label-info">{{ $shift->shift->name ?? 'General Shift (9 AM - 6 PM)' }}</span></p>
        </div>
        <div>
            @if(!$todayAttendance)
                <form method="POST" action="{{ route('portal::attendance.checkIn') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-login me-1"></i> Clock In Now</button>
                </form>
            @elseif(is_null($todayAttendance->check_out) || $todayAttendance->check_out === '00:00:00')
                <form method="POST" action="{{ route('portal::attendance.checkOut') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning"><i class="icon-base ti tabler-logout me-1"></i> Clock Out Now</button>
                </form>
            @else
                <span class="badge bg-label-success fs-6 py-2 px-3"><i class="icon-base ti tabler-circle-check me-1"></i> Day Completed</span>
            @endif
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('portal::attendance.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('portal::attendance.index') }}" class="btn btn-label-secondary w-100"><i class="icon-base ti tabler-refresh me-1"></i> Current</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Logs Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Logged Hours</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($logs as $log)
                        <tr>
                            <td><span class="fw-bold text-heading">{{ \Carbon\Carbon::parse($log->attendance_date)->format('D, M d, Y') }}</span></td>
                            <td>{{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('h:i A') : '--' }}</td>
                            <td>{{ $log->check_out && $log->check_out !== '00:00:00' ? \Carbon\Carbon::parse($log->check_out)->format('h:i A') : '--' }}</td>
                            <td><span class="fw-medium">{{ $log->working_hours }} hrs</span></td>
                            <td>
                                @if($log->status === 'present')
                                    <span class="badge bg-label-success">Present</span>
                                @elseif($log->status === 'late')
                                    <span class="badge bg-label-warning">Late</span>
                                @elseif($log->status === 'half_day')
                                    <span class="badge bg-label-info">Half Day</span>
                                @else
                                    <span class="badge bg-label-danger">Absent</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $log->notes ?? '--' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No attendance logs found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection

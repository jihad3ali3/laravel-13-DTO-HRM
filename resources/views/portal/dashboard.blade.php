@extends('layouts.admin-layout')

@section('title', 'Employee Self-Service Portal')

@section('container')
    <!-- Welcome Header & Live Clock Action -->
    <div class="card mb-6 border-0 bg-primary text-white shadow-sm overflow-hidden">
        <div class="card-body p-6">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h3 class="text-white mb-1 fw-bold">Hello, {{ $employee->first_name }}! 👋</h3>
                    <p class="mb-0 text-white-50">
                        {{ $employee->designation->name ?? 'Team Member' }} &bull; {{ $employee->department->name ?? 'HRM Enterprise' }} ({{ $employee->employee_code }})
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if(!$todayAttendance)
                        <form method="POST" action="{{ route('portal::attendance.checkIn') }}">
                            @csrf
                            <button type="submit" class="btn btn-white text-primary fw-bold">
                                <i class="icon-base ti tabler-login me-1"></i> Clock In Today
                            </button>
                        </form>
                    @elseif(is_null($todayAttendance->check_out) || $todayAttendance->check_out === '00:00:00')
                        <form method="POST" action="{{ route('portal::attendance.checkOut') }}">
                            @csrf
                            <button type="submit" class="btn btn-warning text-white fw-bold">
                                <i class="icon-base ti tabler-logout me-1"></i> Clock Out (In: {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }})
                            </button>
                        </form>
                    @else
                        <span class="badge bg-white text-success fs-6 py-2 px-3">
                            <i class="icon-base ti tabler-circle-check me-1"></i> Shift Completed ({{ $todayAttendance->working_hours }} hrs)
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Summary Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $presentDays }} Days</h4>
                        <small class="text-muted">Present This Month</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-calendar-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-info">{{ $totalHours }} hrs</h4>
                        <small class="text-muted">Total Hours Logged</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-clock"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $usedLeaves }} Used</h4>
                        <small class="text-muted">Approved Leaves ({{ date('Y') }})</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-calendar-off"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $pendingClaims }} Claims</h4>
                        <small class="text-muted">Pending Reimbursements</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-receipt"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active OKRs & Announcements -->
    <div class="row g-6 mb-6">
        <!-- Active Goals -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="icon-base ti tabler-target me-2 text-primary"></i>My Active Goals &amp; OKRs</h5>
                    <a href="{{ route('portal::performance.index') }}" class="btn btn-sm btn-label-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($activeGoals as $goal)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 small fw-bold">{{ $goal->title }}</h6>
                                <span class="badge bg-label-primary">{{ $goal->progress }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $goal->progress }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0">No active goals currently assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Latest Notices -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="icon-base ti tabler-speakerphone me-2 text-warning"></i>Company Notice Board</h5>
                </div>
                <div class="card-body">
                    @forelse($notices as $an)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 fw-bold text-heading small">{{ $an->title }}</h6>
                                <span class="badge bg-label-{{ $an->priority === 'high' ? 'danger' : 'info' }} text-uppercase">{{ $an->priority }}</span>
                            </div>
                            <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit($an->content, 85) }}</p>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0">No notices posted recently.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin-layout')

@section('title', 'Headcount Analytics Report')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Headcount Analytics &amp; Workforce Demographics</h4>
            <p class="mb-0 text-muted">Analysis of active employee headcount and departmental distribution</p>
        </div>
        <a href="{{ route('admin::reports.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Summary KPI -->
    <div class="row g-6 mb-6">
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 fw-bold text-primary">{{ $data['total_active_headcount'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted fw-medium">Total Active Workforce</p>
                    </div>
                    <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-primary fs-3"><i class="icon-base ti tabler-users"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Headcount Breakdown Table -->
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Departmental Distribution</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Headcount</th>
                        <th>Workforce Percentage</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php $total = max(1, $data['total_active_headcount'] ?? 1); @endphp
                    @forelse($data['department_breakdown'] ?? [] as $deptName => $count)
                        @php $pct = round(($count / $total) * 100, 1); @endphp
                        <tr>
                            <td><span class="fw-bold text-heading">{{ $deptName }}</span></td>
                            <td><span class="badge bg-label-primary fs-6">{{ $count }}</span></td>
                            <td style="min-width: 200px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="small fw-medium">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No department data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

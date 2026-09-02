@extends('layouts.admin-layout')

@section('title', 'Attendance Reports')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Attendance &amp; Punctuality Report</h4>
            <p class="mb-0 text-muted">Detailed overview of check-in punctuality, late arrivals, half days, and absentees</p>
        </div>
        <a href="{{ route('admin::reports.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Date Filter -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::reports.attendance') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-success">{{ $data['present'] ?? 0 }}</h3>
                    <small class="text-muted">Present On Time</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-warning">{{ $data['late'] ?? 0 }}</h3>
                    <small class="text-muted">Late Arrivals</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-info">{{ $data['half_day'] ?? 0 }}</h3>
                    <small class="text-muted">Half Day Entries</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-danger">{{ $data['absent'] ?? 0 }}</h3>
                    <small class="text-muted">Absentees</small>
                </div>
            </div>
        </div>
    </div>
@endsection

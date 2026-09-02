@extends('layouts.admin-layout')

@section('title', 'Leave Utilization Report')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Leave Utilization Report ({{ $data['year'] ?? date('Y') }})</h4>
            <p class="mb-0 text-muted">Analysis of annual leave requests, approval rates, and pending pipeline</p>
        </div>
        <a href="{{ route('admin::reports.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-primary">{{ $data['total_requests'] ?? 0 }}</h3>
                    <small class="text-muted">Total Leave Applications</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-success">{{ $data['approved'] ?? 0 }}</h3>
                    <small class="text-muted">Approved Leaves</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-warning">{{ $data['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending In Review</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-danger">{{ $data['rejected'] ?? 0 }}</h3>
                    <small class="text-muted">Rejected Requests</small>
                </div>
            </div>
        </div>
    </div>
@endsection

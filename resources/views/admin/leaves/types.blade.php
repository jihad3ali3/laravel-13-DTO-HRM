@extends('layouts.admin-layout')

@section('title', 'Leave Policies & Types')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Leave Policies &amp; Types</h4>
            <p class="mb-0 text-muted">View organizational leave categories, yearly quotas, and paid/unpaid status</p>
        </div>
        <a href="{{ route('admin::leaves.requests') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Leave Requests
        </a>
    </div>

    <!-- Leave Types Grid -->
    <div class="row g-4">
        @forelse($leaveTypes as $type)
            <div class="col-md-4">
                <div class="card h-100 card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">{{ $type->name }}</h5>
                            <span class="badge bg-label-info">{{ $type->code }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="avatar avatar-md"><span class="avatar-initial rounded bg-label-primary fs-4"><i class="icon-base ti tabler-calendar-due"></i></span></div>
                            <div>
                                <h3 class="mb-0 fw-bold text-primary">{{ $type->days_allowed }}</h3>
                                <small class="text-muted">Days Entitlement / Year</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="text-muted">Type:</span>
                            @if($type->is_paid)
                                <span class="badge bg-label-success"><i class="icon-base ti tabler-check me-1"></i> Paid Leave</span>
                            @else
                                <span class="badge bg-label-secondary">Unpaid Leave</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    No leave policies defined.
                </div>
            </div>
        @endforelse
    </div>
@endsection

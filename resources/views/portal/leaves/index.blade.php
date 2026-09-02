@extends('layouts.admin-layout')

@section('title', 'My Leaves & Quotas')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">My Leaves &amp; Annual Quotas</h4>
            <p class="mb-0 text-muted">Track remaining entitlements and apply for time off</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            <i class="icon-base ti tabler-plus me-1"></i> Apply For Leave
        </button>
    </div>

    <!-- Leave Balances Cards -->
    <div class="row g-4 mb-6">
        @foreach($balances as $bal)
            <div class="col-md-4">
                <div class="card h-100 card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">{{ $bal['type']->name }}</h6>
                            <span class="badge bg-label-info">{{ $bal['type']->code }}</span>
                        </div>
                        <h3 class="mb-1 fw-bold text-primary">{{ $bal['remaining'] }} <small class="text-muted fs-6">Days Left</small></h3>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Allowed: <strong>{{ $bal['allowed'] }}</strong></span>
                            <span>Used: <strong>{{ $bal['used'] }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Leave Requests History -->
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">My Leave Applications History</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leaveRequests as $req)
                        @php
                            $start = \Carbon\Carbon::parse($req->start_date);
                            $end = \Carbon\Carbon::parse($req->end_date);
                            $days = $start->diffInDays($end) + 1;
                        @endphp
                        <tr>
                            <td><span class="fw-bold text-heading">{{ $req->leave->name ?? 'Standard Leave' }}</span></td>
                            <td>{{ $start->format('M d, Y') }} &rarr; {{ $end->format('M d, Y') }}</td>
                            <td><span class="badge bg-label-secondary">{{ $days }} Day(s)</span></td>
                            <td><small class="text-muted">{{ $req->reason }}</small></td>
                            <td>
                                @if($req->status === 'approved')
                                    <span class="badge bg-label-success">Approved</span>
                                @elseif($req->status === 'rejected')
                                    <span class="badge bg-label-danger">Rejected</span>
                                @else
                                    <span class="badge bg-label-warning">Pending Review</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No leave applications submitted yet.</td></tr>
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
            <form method="POST" action="{{ route('portal::leaves.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Time Off</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Leave Category</label>
                        <select name="leave_id" class="form-select" required>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $balances[$lt->id]['remaining'] ?? 0 }} remaining)</option>
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
                        <label class="form-label required">Reason for Absence</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Brief explanation for your manager..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-send me-1"></i> Submit Request</button>
                </div>
            </form>
        </div>
    </div>
@endsection

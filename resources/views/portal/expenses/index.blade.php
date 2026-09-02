@extends('layouts.admin-layout')

@section('title', 'My Reimbursement Claims')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">My Expense Claims &amp; Reimbursements</h4>
            <p class="mb-0 text-muted">Submit official business expense receipts and monitor payout status</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitClaimModal">
            <i class="icon-base ti tabler-receipt-plus me-1"></i> Submit Expense Claim
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-md-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-primary">₹{{ number_format($totalSubmitted, 2) }}</h3>
                    <small class="text-muted">Total Submitted Claims</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-success">₹{{ number_format($totalReimbursed, 2) }}</h3>
                    <small class="text-muted">Total Reimbursed</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <h3 class="mb-0 fw-bold text-warning">{{ $pendingCount }}</h3>
                    <small class="text-muted">Claims Under Review</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Claims Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Claim ID &amp; Title</th>
                        <th>Category</th>
                        <th>Expense Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($claims as $cl)
                        <tr>
                            <td>
                                <span class="fw-bold text-heading d-block">{{ $cl->title }}</span>
                                <small class="text-muted">{{ $cl->claim_code }}</small>
                            </td>
                            <td><span class="badge bg-label-info text-capitalize">{{ $cl->category }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($cl->expense_date)->format('M d, Y') }}</td>
                            <td><span class="fw-bold text-heading">₹{{ number_format($cl->amount, 2) }}</span></td>
                            <td>
                                @if($cl->status === 'reimbursed')
                                    <span class="badge bg-label-success"><i class="icon-base ti tabler-check me-1"></i> Reimbursed</span>
                                @elseif($cl->status === 'accepted')
                                    <span class="badge bg-label-info">Accepted (Pending Payment)</span>
                                @elseif($cl->status === 'rejected')
                                    <span class="badge bg-label-danger" title="{{ $cl->reject_reason }}">Rejected</span>
                                @else
                                    <span class="badge bg-label-warning">In Review</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No reimbursement claims filed yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($claims->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $claims->links() }}
            </div>
        @endif
    </div>

    <!-- Submit Claim Modal -->
    <div class="modal fade" id="submitClaimModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('portal::expenses.store') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Submit Business Expense Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Expense Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Client Dinner / Transport Fare" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="travel">Travel &amp; Commute</option>
                                <option value="meals">Meals &amp; Hospitality</option>
                                <option value="training">Courses &amp; Certification</option>
                                <option value="equipment">Hardware / Workstation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="1500.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Expense Date</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt / Invoice File</label>
                        <input type="file" name="receipt" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Business Justification</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Context for expense..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-send me-1"></i> Submit Claim</button>
                </div>
            </form>
        </div>
    </div>
@endsection

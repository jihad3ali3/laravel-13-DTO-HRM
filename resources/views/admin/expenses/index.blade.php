@extends('layouts.admin-layout')

@section('title', 'Expense Claims')

@section('container')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h4 class="mb-1">Expense Claims &amp; Reimbursements</h4>
            <p class="mb-0 text-muted">Review employee business claims, verify receipts, and manage disbursements</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin::export.expenses.csv', request()->query()) }}" class="btn btn-label-success">
                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Export Claims
            </a>
            @if(auth()->user()->can('expenses.create') || auth()->user()->employee)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                    <i class="icon-base ti tabler-plus me-1"></i> Submit Claim
                </button>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $pendingCount }}</h4>
                        <small class="text-muted">Pending Review</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-clock-hour-4"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-info">{{ $acceptedCount }}</h4>
                        <small class="text-muted">Accepted Claims</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-thumb-up"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $reimbursedCount }}</h4>
                        <small class="text-muted">Reimbursed Claims</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-circle-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">₹{{ number_format($totalReimbursed, 2) }}</h4>
                        <small class="text-muted">Total Reimbursed</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-receipt"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::expenses.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="travel" {{ request('category') === 'travel' ? 'selected' : '' }}>Travel</option>
                        <option value="meals" {{ request('category') === 'meals' ? 'selected' : '' }}>Meals &amp; Dining</option>
                        <option value="stationery" {{ request('category') === 'stationery' ? 'selected' : '' }}>Office Supplies</option>
                        <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Hardware / Equipment</option>
                        <option value="training" {{ request('category') === 'training' ? 'selected' : '' }}>Training &amp; Courses</option>
                        <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="reimbursed" {{ request('status') === 'reimbursed' ? 'selected' : '' }}>Reimbursed</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::expenses.index') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expense Claims Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Claim Details</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($claims as $claim)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($claim->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $claim->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $claim->employee->first_name ?? 'Employee' }} {{ $claim->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $claim->claim_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block text-heading fw-medium small">{{ $claim->title }}</span>
                                <span class="badge bg-label-secondary text-capitalize">{{ $claim->category }}</span>
                            </td>
                            <td>
                                <span class="small">{{ \Carbon\Carbon::parse($claim->expense_date)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-heading">₹{{ number_format($claim->amount, 2) }}</span>
                            </td>
                            <td>
                                @if($claim->status === 'reimbursed')
                                    <span class="badge bg-label-success">Reimbursed</span>
                                @elseif($claim->status === 'accepted')
                                    <span class="badge bg-label-info">Accepted</span>
                                @elseif($claim->status === 'rejected')
                                    <span class="badge bg-label-danger" title="{{ $claim->reject_reason }}">Rejected</span>
                                @else
                                    <span class="badge bg-label-warning">Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('expenses.approve')
                                    @if($claim->status === 'pending')
                                        <div class="d-flex justify-content-end gap-1">
                                            <form method="POST" action="{{ route('admin::expenses.process', $claim->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-info" title="Accept Claim">
                                                    <i class="icon-base ti tabler-thumb-up"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin::expenses.process', $claim->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Reject Claim">
                                                    <i class="icon-base ti tabler-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($claim->status === 'accepted')
                                        <form method="POST" action="{{ route('admin::expenses.process', $claim->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="reimbursed">
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark Reimbursed">
                                                <i class="icon-base ti tabler-check me-1"></i> Reimburse
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Completed</span>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No expense claims found.</td>
                        </tr>
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

    <!-- Create Expense Claim Modal -->
    <div class="modal fade" id="createClaimModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::expenses.store') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Submit Expense Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(auth()->user()->can('expenses.manage') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
                        <div class="mb-3">
                            <label class="form-label required">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (auth()->user()->employee?->id === $emp->id) ? 'selected' : '' }}>
                                        {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                        <div class="alert alert-primary py-2 px-3 mb-3">
                            <small><i class="icon-base ti tabler-user me-1"></i> Submitting claim for: <strong>{{ auth()->user()->name }}</strong></small>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label required">Claim Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Client Dinner / Train Tickets" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="travel">Travel</option>
                                <option value="meals">Meals</option>
                                <option value="stationery">Stationery</option>
                                <option value="equipment">Equipment</option>
                                <option value="training">Training</option>
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
                        <label class="form-label">Receipt File (Optional)</label>
                        <input type="file" name="receipt" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Remarks</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Brief justification for expense..."></textarea>
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

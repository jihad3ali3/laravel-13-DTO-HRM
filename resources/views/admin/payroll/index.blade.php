@extends('layouts.admin-layout')

@section('title', 'Payroll Batches')

@section('container')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h4 class="mb-1">Monthly Payroll Processing</h4>
            <p class="mb-0 text-muted">Generate batch payrolls, calculate salary structures, and manage payouts</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin::export.payroll.csv', request()->query()) }}" class="btn btn-label-success">
                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Export CSV
            </a>
            <a href="{{ route('admin::export.payroll.print', request()->query()) }}" target="_blank" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-printer me-1"></i> Print Statement
            </a>
            @can('payroll.generate')
                <a href="{{ route('admin::payroll.structures') }}" class="btn btn-label-secondary">
                    <i class="icon-base ti tabler-adjustments me-1"></i> Salary Structures
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
                    <i class="icon-base ti tabler-calculator me-1"></i> Generate Payroll
                </button>
            @endcan
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">₹{{ number_format($totalDisbursed, 2) }}</h4>
                        <small class="text-muted">Total Disbursed (Paid)</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-circle-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">₹{{ number_format($totalPending, 2) }}</h4>
                        <small class="text-muted">Draft / Pending Payout</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-clock-hour-4"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $paidCount }}</h4>
                        <small class="text-muted">Disbursed Records</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-file-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $draftCount }}</h4>
                        <small class="text-muted">Draft Batches</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-secondary"><i class="icon-base ti tabler-file-invoice"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::payroll.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::payroll.index') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($payrolls as $pay)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($pay->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $pay->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $pay->employee->first_name ?? 'Employee' }} {{ $pay->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $pay->payroll_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ date('F', mktime(0, 0, 0, $pay->month, 1)) }} {{ $pay->year }}</span>
                            </td>
                            <td>₹{{ number_format($pay->basic_salary, 2) }}</td>
                            <td><span class="text-success">+₹{{ number_format($pay->total_allowance, 2) }}</span></td>
                            <td><span class="text-danger">-₹{{ number_format($pay->total_deduction, 2) }}</span></td>
                            <td><span class="fw-bold text-success fs-6">₹{{ number_format($pay->net_salary, 2) }}</span></td>
                            <td>
                                @if($pay->status === 'paid')
                                    <span class="badge bg-label-success"><i class="icon-base ti tabler-check me-1"></i> Paid</span>
                                @else
                                    <span class="badge bg-label-warning">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin::payroll.payslip', $pay->id) }}" class="btn btn-sm btn-icon btn-label-secondary" title="View Payslip">
                                        <i class="icon-base ti tabler-file-invoice"></i>
                                    </a>
                                    @can('payroll.pay')
                                        @if($pay->status !== 'paid')
                                            <form method="POST" action="{{ route('admin::payroll.pay', $pay->id) }}" onsubmit="return confirm('Mark this payroll as paid?');" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-success" title="Mark as Paid">
                                                    <i class="icon-base ti tabler-cash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="icon-base ti tabler-file-dollar icon-48px text-muted mb-2"></i>
                                <h6>No payroll records found for the selected period.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>

    <!-- Generate Payroll Modal -->
    <div class="modal fade" id="generatePayrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::payroll.generate') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Monthly Payroll Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">This action will compute basic salaries, allowances, and statutory deductions for all active employees for the selected month.</p>
                    <div class="mb-3">
                        <label class="form-label required">Month</label>
                        <select name="month" class="form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-calculator me-1"></i> Generate Batch</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.admin-layout')

@section('title', 'My Payslips & Compensation')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">My Payslips &amp; Compensation</h4>
            <p class="mb-0 text-muted">Access your monthly salary slips, tax deductions, and benefits breakdown</p>
        </div>
    </div>

    <!-- Salary Structure Overview Card -->
    @if($structure)
        <div class="card mb-6 card-border-shadow-primary">
            <div class="card-header border-bottom"><h5 class="card-title mb-0">Salary Breakdown Overview</h5></div>
            <div class="card-body pt-4">
                <div class="row g-4 text-center text-sm-start">
                    <div class="col-sm-6 col-md-3 border-end">
                        <small class="text-muted d-block">Basic Salary</small>
                        <h4 class="mb-0 fw-bold text-heading">₹{{ number_format($structure->basic_salary, 2) }}</h4>
                    </div>
                    <div class="col-sm-6 col-md-3 border-end">
                        <small class="text-muted d-block">House Rent (HRA)</small>
                        <h4 class="mb-0 fw-bold text-heading">₹{{ number_format($structure->hra, 2) }}</h4>
                    </div>
                    <div class="col-sm-6 col-md-3 border-end">
                        <small class="text-muted d-block">Total Allowances</small>
                        <h4 class="mb-0 fw-bold text-success">+₹{{ number_format($structure->conveyance_allowance + $structure->medical_allowance + $structure->special_allowance, 2) }}</h4>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <small class="text-muted d-block">Net Monthly Take-Home</small>
                        <h4 class="mb-0 fw-bold text-primary">₹{{ number_format($structure->net_salary, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payslip History Table -->
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Disbursed Salary History</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Reference Code</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Take-Home</th>
                        <th>Disbursement Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($payrolls as $pay)
                        <tr>
                            <td><span class="badge bg-label-primary fs-6">{{ date('F', mktime(0, 0, 0, $pay->month, 1)) }} {{ $pay->year }}</span></td>
                            <td><code>{{ $pay->payroll_code }}</code></td>
                            <td>₹{{ number_format($pay->basic_salary + $pay->total_allowance, 2) }}</td>
                            <td><span class="text-danger">-₹{{ number_format($pay->total_deduction, 2) }}</span></td>
                            <td><span class="fw-bold text-success fs-6">₹{{ number_format($pay->net_salary, 2) }}</span></td>
                            <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('M d, Y') : 'Processing' }}</td>
                            <td class="text-end">
                                <a href="{{ route('portal::payroll.payslip', $pay->id) }}" class="btn btn-sm btn-primary">
                                    <i class="icon-base ti tabler-printer me-1"></i> View / Print Slip
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No salary slips generated yet.</td></tr>
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
@endsection

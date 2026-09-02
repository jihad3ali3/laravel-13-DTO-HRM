@extends('layouts.admin-layout')

@section('title', 'Salary Slip - ' . $payroll->payroll_code)

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6 d-print-none">
        <div>
            <h4 class="mb-1">Official Payslip</h4>
            <p class="mb-0 text-muted">Disbursement reference: <span class="badge bg-label-info">{{ $payroll->payroll_code }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="icon-base ti tabler-printer me-1"></i> Print Payslip
            </button>
            <a href="{{ route('admin::payroll.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Payroll
            </a>
        </div>
    </div>

    <!-- Printable Payslip Card -->
    <div class="card p-6 p-md-8">
        <!-- Payslip Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-6">
            <div>
                <h3 class="mb-1 fw-bold text-primary">HRM ENTERPRISE</h3>
                <p class="mb-0 text-muted">Company Payroll &amp; Compensation Department</p>
            </div>
            <div class="text-end">
                <h4 class="mb-1 fw-bold">SALARY SLIP</h4>
                <p class="mb-0 text-muted">Period: <strong>{{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }} {{ $payroll->year }}</strong></p>
                <span class="badge bg-label-{{ $payroll->status === 'paid' ? 'success' : 'warning' }} mt-1 text-uppercase">{{ $payroll->status }}</span>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="row g-4 mb-6 pb-4 border-bottom">
            <div class="col-sm-6">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-0">Employee Name:</td>
                            <td class="fw-bold">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Employee Code:</td>
                            <td>{{ $payroll->employee->employee_code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Department:</td>
                            <td>{{ $payroll->employee->department->name ?? 'General' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-0">Designation:</td>
                            <td>{{ $payroll->employee->designation->title ?? 'General' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Disbursement Date:</td>
                            <td>{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('d M, Y') : 'Pending' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Payment Method:</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $payroll->payment_method ?? 'Bank Transfer') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Earnings & Deductions Breakdown Table -->
        <div class="row g-6 mb-6">
            <div class="col-md-6">
                <div class="card bg-label-success border-0 shadow-none">
                    <div class="card-header pb-2"><h6 class="mb-0 fw-bold text-success">EARNINGS</h6></div>
                    <div class="card-body pt-0">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-end fw-medium">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowances (HRA, Travel, Medical)</td>
                                    <td class="text-end fw-medium">₹{{ number_format($payroll->total_allowance, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-success">Total Gross Earnings</td>
                                    <td class="text-end fw-bold text-success">₹{{ number_format($payroll->basic_salary + $payroll->total_allowance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-label-danger border-0 shadow-none">
                    <div class="card-header pb-2"><h6 class="mb-0 fw-bold text-danger">DEDUCTIONS</h6></div>
                    <div class="card-body pt-0">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td>Provident Fund (PF) &amp; Tax</td>
                                    <td class="text-end fw-medium">₹{{ number_format($payroll->total_deduction, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Other Deductions</td>
                                    <td class="text-end fw-medium">₹0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-danger">Total Deductions</td>
                                    <td class="text-end fw-bold text-danger">₹{{ number_format($payroll->total_deduction, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Pay Banner -->
        <div class="p-4 bg-label-primary rounded d-flex justify-content-between align-items-center mb-6">
            <div>
                <h5 class="mb-0 fw-bold text-primary">NET TAKE-HOME SALARY</h5>
                <small class="text-muted">Total Gross Earnings minus Total Deductions</small>
            </div>
            <h3 class="mb-0 fw-bold text-primary">₹{{ number_format($payroll->net_salary, 2) }}</h3>
        </div>

        <div class="text-center text-muted small mt-4 pt-4 border-top">
            <p class="mb-0">This is a computer-generated salary slip and does not require a physical signature.</p>
        </div>
    </div>
@endsection

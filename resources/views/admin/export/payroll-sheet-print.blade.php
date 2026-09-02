<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Monthly Payroll Statement — HRM Enterprise</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <style>
        body { background: #fff !important; color: #111; font-size: 12px; }
        .print-header { border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 20px; }
        .table-bordered th, .table-bordered td { border: 1px solid #ddd !important; padding: 6px 8px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            @page { size: landscape; margin: 10mm; }
        }
    </style>
</head>
<body class="p-4">
    <!-- Action Bar -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
        <div>
            <h5 class="mb-0 fw-bold">Official Monthly Payroll Statement</h5>
            <small class="text-muted">Disbursement Cycle: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="icon-base ti tabler-printer me-1"></i> Print / Save as PDF</button>
            <button onclick="window.close()" class="btn btn-label-secondary btn-sm">Close</button>
        </div>
    </div>

    <!-- Letterhead -->
    <div class="print-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">HRM Enterprise Inc.</h3>
            <p class="mb-0 text-muted">Monthly Payroll Register &bull; Period: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
        </div>
        <div class="text-end">
            <h5 class="mb-0 text-success fw-bold">Total Disbursed: ₹{{ number_format($totalDisbursed, 2) }}</h5>
            <small class="text-muted">Total Deductions: ₹{{ number_format($totalDeductions, 2) }}</small>
        </div>
    </div>

    <!-- Payroll Table -->
    <table class="table table-bordered table-sm table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Payroll Code</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th class="text-end">Basic (₹)</th>
                <th class="text-end">Allowances (₹)</th>
                <th class="text-end">Deductions (₹)</th>
                <th class="text-end">Net Pay (₹)</th>
                <th>Status</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $idx => $p)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><code>{{ $p->payroll_code }}</code></td>
                    <td><strong>{{ $p->employee->employee_code ?? '' }}</strong></td>
                    <td>{{ $p->employee->first_name ?? '' }} {{ $p->employee->last_name ?? '' }}</td>
                    <td>{{ $p->employee->department->name ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format((float) $p->basic_salary, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $p->total_allowance, 2) }}</td>
                    <td class="text-end text-danger">-{{ number_format((float) $p->total_deduction, 2) }}</td>
                    <td class="text-end fw-bold text-success">₹{{ number_format((float) $p->net_salary, 2) }}</td>
                    <td>
                        <span class="badge {{ $p->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M, Y') : '--' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center py-4 text-muted">No payroll records for this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="row mt-6 pt-4 border-top">
        <div class="col-4 text-center">
            <p class="mb-5">Prepared By:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">Payroll Officer</small>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Checked &amp; Audited:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">Finance &amp; Tax Lead</small>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Authorized Signatory:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">Chief Financial Officer</small>
        </div>
    </div>
</body>
</html>

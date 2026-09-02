<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Employee Master Roll — HRM Enterprise</title>
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
            <h5 class="mb-0 fw-bold">Official Employee Master Register</h5>
            <small class="text-muted">Total Headcount: {{ $employees->count() }} Employees</small>
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
            <p class="mb-0 text-muted">Corporate Workforce Master Roll &bull; Confidential &bull; Generated: {{ date('d M, Y H:i') }}</p>
        </div>
        <div class="text-end">
            <h6 class="mb-0 fw-bold">Total Staff: {{ $employees->count() }}</h6>
            <small class="text-muted">Active: {{ $employees->where('is_active', true)->count() }}</small>
        </div>
    </div>

    <!-- Employee Table -->
    <table class="table table-bordered table-sm table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Type</th>
                <th>Joining Date</th>
                <th>Contact Phone</th>
                <th>Work Email</th>
                <th class="text-end">Monthly Pay (₹)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $idx => $emp)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $emp->employee_code }}</strong></td>
                    <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                    <td>{{ $emp->department->name ?? 'N/A' }}</td>
                    <td>{{ $emp->designation->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $emp->employment_type)) }}</td>
                    <td>{{ $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date)->format('d M, Y') : '--' }}</td>
                    <td>{{ $emp->phone ?? '--' }}</td>
                    <td>{{ $emp->email }}</td>
                    <td class="text-end fw-bold">{{ number_format((float) $emp->salary, 2) }}</td>
                    <td>
                        <span class="badge {{ $emp->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $emp->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signature Footer -->
    <div class="row mt-6 pt-4 border-top">
        <div class="col-4 text-center">
            <p class="mb-5">Prepared By:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">HR Operations Executive</small>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Verified By:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">Head of People Strategy</small>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5">Approved By:</p>
            <hr class="w-75 mx-auto">
            <small class="text-muted">Chief Executive / Director</small>
        </div>
    </div>
</body>
</html>

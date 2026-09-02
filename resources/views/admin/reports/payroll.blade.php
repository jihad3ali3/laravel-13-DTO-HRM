@extends('layouts.admin-layout')

@section('title', 'Payroll Analytics Report')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Annual Payroll Analytics ({{ $data['year'] ?? date('Y') }})</h4>
            <p class="mb-0 text-muted">Disbursed salary disbursements and monthly expense analysis</p>
        </div>
        <a href="{{ route('admin::reports.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Reports
        </a>
    </div>

    <!-- Annual Total KPI -->
    <div class="row g-6 mb-6">
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 fw-bold text-info">₹{{ number_format($data['total_annual_disbursed'] ?? 0, 2) }}</h3>
                        <p class="mb-0 text-muted fw-medium">Total Annual Disbursed Payroll</p>
                    </div>
                    <div class="avatar avatar-lg"><span class="avatar-initial rounded bg-label-info fs-3"><i class="icon-base ti tabler-file-dollar"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Monthly Disbursement Breakdown</h5></div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Status</th>
                        <th class="text-end">Total Disbursed Net Salary</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @for($m = 1; $m <= 12; $m++)
                        @php $sum = $data['monthly_breakdown'][$m] ?? 0; @endphp
                        <tr>
                            <td><span class="fw-bold text-heading">{{ date('F', mktime(0, 0, 0, $m, 1)) }} {{ $data['year'] }}</span></td>
                            <td>
                                @if($sum > 0)
                                    <span class="badge bg-label-success">Disbursed</span>
                                @else
                                    <span class="badge bg-label-secondary">No Records</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $sum > 0 ? 'text-success' : 'text-muted' }}">
                                ₹{{ number_format($sum, 2) }}
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection

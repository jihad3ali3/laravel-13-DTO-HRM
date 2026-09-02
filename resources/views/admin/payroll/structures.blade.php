@extends('layouts.admin-layout')

@section('title', 'Salary Structures')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Employee Salary Structures</h4>
            <p class="mb-0 text-muted">Configure base pay, allowances (HRA, Conveyance, Medical), and deductions (PF, Tax)</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editStructureModal">
                <i class="icon-base ti tabler-edit me-1"></i> Update Salary Structure
            </button>
            <a href="{{ route('admin::payroll.index') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Payroll
            </a>
        </div>
    </div>

    <!-- Salary Structures Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department &amp; Role</th>
                        <th>Basic Pay</th>
                        <th>HRA</th>
                        <th>Allowances</th>
                        <th>PF / Tax</th>
                        <th>Net Monthly Pay</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($structures as $st)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($st->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $st->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $st->employee->first_name ?? 'Employee' }} {{ $st->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $st->employee->employee_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $st->employee->department->name ?? 'General' }}</span>
                                <small class="d-block text-muted">{{ $st->employee->designation->title ?? 'Staff' }}</small>
                            </td>
                            <td>₹{{ number_format($st->basic_salary, 2) }}</td>
                            <td>₹{{ number_format($st->hra, 2) }}</td>
                            <td>
                                <span class="text-success">+₹{{ number_format($st->conveyance_allowance + $st->medical_allowance + $st->special_allowance, 2) }}</span>
                            </td>
                            <td>
                                <span class="text-danger">-₹{{ number_format($st->provident_fund + $st->professional_tax, 2) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">₹{{ number_format($st->net_salary, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No salary structures configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($structures->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $structures->links() }}
            </div>
        @endif
    </div>

    <!-- Update Structure Modal -->
    <div class="modal fade" id="editStructureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('admin::payroll.structures.update') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Configure Employee Salary Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label required">Select Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Choose Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Basic Salary (₹)</label>
                            <input type="number" step="0.01" name="basic_salary" class="form-control" placeholder="50000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">House Rent Allowance (HRA)</label>
                            <input type="number" step="0.01" name="hra" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Conveyance Allowance</label>
                            <input type="number" step="0.01" name="conveyance_allowance" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Medical Allowance</label>
                            <input type="number" step="0.01" name="medical_allowance" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Special Allowance</label>
                            <input type="number" step="0.01" name="special_allowance" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Provident Fund (PF)</label>
                            <input type="number" step="0.01" name="provident_fund" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Professional Tax</label>
                            <input type="number" step="0.01" name="professional_tax" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-device-floppy me-1"></i> Save Structure</button>
                </div>
            </form>
        </div>
    </div>
@endsection

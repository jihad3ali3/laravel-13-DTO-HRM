@extends('layouts.admin-layout')

@section('title', 'Employee Directory')

@section('container')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h4 class="mb-1">Employee Directory</h4>
            <p class="mb-0 text-muted">Manage all employee records, profiles, and employment details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin::export.employees.csv', request()->query()) }}" class="btn btn-label-success">
                <i class="icon-base ti tabler-file-spreadsheet me-1"></i> Export CSV
            </a>
            <a href="{{ route('admin::export.employees.print') }}" target="_blank" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-printer me-1"></i> Master Roll
            </a>
            @can('employees.create')
                <a href="{{ route('admin::employees.create') }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-user-plus me-1"></i> Add New Employee
                </a>
            @endcan
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::employees.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Name, Email, Code..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Designation</label>
                    <select name="designation_id" class="form-select">
                        <option value="">All Designations</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>
                                {{ $desig->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::employees.index') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Table Card -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Contact</th>
                        <th>Department &amp; Role</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($employees as $employee)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $employee->id) }}" class="fw-bold text-heading d-block">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </a>
                                        <span class="badge bg-label-info">{{ $employee->employee_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block text-heading small">{{ $employee->email }}</span>
                                <small class="text-muted">{{ $employee->phone ?? 'No Phone' }}</small>
                            </td>
                            <td>
                                <span class="fw-medium text-heading d-block">{{ $employee->department->name ?? 'Unassigned' }}</span>
                                <small class="text-muted">{{ $employee->designation->title ?? 'General' }}</small>
                            </td>
                            <td>
                                <span class="small">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') }}</span>
                            </td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-text-secondary rounded-pill p-1" data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin::employees.show', $employee->id) }}">
                                                <i class="icon-base ti tabler-eye me-2"></i> View Profile
                                            </a>
                                        </li>
                                        @can('employees.edit')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin::employees.edit', $employee->id) }}">
                                                <i class="icon-base ti tabler-pencil me-2"></i> Edit Details
                                            </a>
                                        </li>
                                        @endcan
                                        @can('employees.delete')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin::employees.destroy', $employee->id) }}" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="icon-base ti tabler-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="icon-base ti tabler-users-minus icon-48px text-muted mb-2"></i>
                                <h6 class="mb-0 text-muted">No employees found matching your criteria.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
@endsection

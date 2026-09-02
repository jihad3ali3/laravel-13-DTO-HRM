@extends('layouts.admin-layout')

@section('title', 'Add New Employee')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Onboard New Employee</h4>
            <p class="mb-0 text-muted">Create a employee profile, allocate role, and set initial compensation</p>
        </div>
        <a href="{{ route('admin::employees.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Directory
        </a>
    </div>

    <form method="POST" action="{{ route('admin::employees.store') }}" class="card">
        @csrf
        <div class="card-body">
            <!-- Section 1: Basic Information -->
            <h5 class="mb-4 text-primary"><i class="icon-base ti tabler-user me-2"></i>1. Personal Information</h5>
            <div class="row g-4 mb-6">
                <div class="col-md-3">
                    <label class="form-label required">Employee Code</label>
                    <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', 'EMP-'.rand(1000, 9999)) }}" required>
                    @error('employee_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label required">First Name</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Official Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Emergency Contact Info</label>
                    <textarea name="emergency_contact" rows="2" class="form-control" placeholder="Name, relation, phone number...">{{ old('emergency_contact') }}</textarea>
                </div>
            </div>

            <hr class="my-6">

            <!-- Section 2: Employment Details -->
            <h5 class="mb-4 text-primary"><i class="icon-base ti tabler-briefcase me-2"></i>2. Company &amp; Job Placement</h5>
            <div class="row g-4 mb-6">
                <div class="col-md-3">
                    <label class="form-label required">Department</label>
                    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Designation</label>
                    <select name="designation_id" class="form-select @error('designation_id') is-invalid @enderror" required>
                        <option value="">Select Designation</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>
                                {{ $desig->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Employment Type</label>
                    <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                        <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Monthly Basic Salary (₹)</label>
                    <input type="number" step="0.01" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', '50000.00') }}" required>
                    @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="my-6">

            <!-- Section 3: Portal Access & Security -->
            <h5 class="mb-4 text-primary"><i class="icon-base ti tabler-shield-lock me-2"></i>3. Portal Access &amp; Credentials</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label required">System Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', 'employee') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Initial Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin::employees.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Register Employee</button>
        </div>
    </form>
@endsection

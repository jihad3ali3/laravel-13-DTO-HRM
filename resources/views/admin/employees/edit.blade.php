@extends('layouts.admin-layout')

@section('title', 'Edit Employee - ' . $employee->first_name . ' ' . $employee->last_name)

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Edit Employee Profile</h4>
            <p class="mb-0 text-muted">Update contact details, job placement, and status for {{ $employee->employee_code }}</p>
        </div>
        <a href="{{ route('admin::employees.show', $employee->id) }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('admin::employees.update', $employee->id) }}" class="card">
        @csrf
        @method('PUT')
        <div class="card-body">
            <!-- Section 1: Basic Information -->
            <h5 class="mb-4 text-primary"><i class="icon-base ti tabler-user me-2"></i>1. Personal Information</h5>
            <div class="row g-4 mb-6">
                <div class="col-md-4">
                    <label class="form-label required">First Name</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $employee->last_name) }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $employee->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Employment Status</label>
                    <select name="is_active" class="form-select" required>
                        <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $employee->address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Emergency Contact</label>
                    <textarea name="emergency_contact" rows="2" class="form-control">{{ old('emergency_contact', $employee->emergency_contact) }}</textarea>
                </div>
            </div>

            <hr class="my-6">

            <!-- Section 2: Employment Details -->
            <h5 class="mb-4 text-primary"><i class="icon-base ti tabler-briefcase me-2"></i>2. Job Placement &amp; Compensation</h5>
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label required">Department</label>
                    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Designation</label>
                    <select name="designation_id" class="form-select @error('designation_id') is-invalid @enderror" required>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ old('designation_id', $employee->designation_id) == $desig->id ? 'selected' : '' }}>
                                {{ $desig->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Employment Type</label>
                    <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                        <option value="full_time" {{ old('employment_type', $employee->employment_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type', $employee->employment_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type', $employee->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="intern" {{ old('employment_type', $employee->employment_type) === 'intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required">Basic Salary (₹)</label>
                    <input type="number" step="0.01" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $employee->salary) }}" required>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin::employees.show', $employee->id) }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-device-floppy me-1"></i> Save Changes</button>
        </div>
    </form>
@endsection

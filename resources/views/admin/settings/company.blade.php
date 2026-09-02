@extends('layouts.admin-layout')

@section('title', 'Company Settings')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Company Profile &amp; Organization Preferences</h4>
            <p class="mb-0 text-muted">Configure corporate identity, tax identification, working policies, and localization</p>
        </div>
    </div>

    <!-- Company Settings Form -->
    <div class="card">
        <div class="card-header border-bottom pb-4 mb-4">
            <h5 class="card-title mb-0">General Organization Details</h5>
        </div>
        <form method="POST" action="{{ route('admin::settings.company.update') }}" class="card-body pt-0">
            @csrf
            @method('PUT')

            <!-- Section 1: Company Profile -->
            <div class="row g-4 mb-6">
                <div class="col-md-6">
                    <label class="form-label required">Company Legal Name</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $companySettings['company_name'] ?? 'HRM Enterprise Inc.') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Corporate Email</label>
                    <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $companySettings['company_email'] ?? 'hr@company.com') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone / Contact</label>
                    <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $companySettings['company_phone'] ?? '+91-11-23456789') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $companySettings['website'] ?? 'https://hrm.example.com') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Corporate Headquarters Address</label>
                    <textarea name="company_address" rows="2" class="form-control">{{ old('company_address', $companySettings['company_address'] ?? 'Tech Park, Cyber Hub, New Delhi, India') }}</textarea>
                </div>
            </div>

            <hr class="my-6">

            <!-- Section 2: Regional & Tax Configurations -->
            <h5 class="card-title mb-4">Localization &amp; Tax Settings</h5>
            <div class="row g-4 mb-6">
                <div class="col-md-4">
                    <label class="form-label required">Currency Code</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency', $companySettings['currency'] ?? 'INR') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Currency Symbol</label>
                    <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $companySettings['currency_symbol'] ?? '₹') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">System Timezone</label>
                    <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $companySettings['timezone'] ?? 'Asia/Kolkata') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date Format</label>
                    <select name="date_format" class="form-select">
                        <option value="Y-m-d" {{ (old('date_format', $companySettings['date_format'] ?? '') === 'Y-m-d') ? 'selected' : '' }}>YYYY-MM-DD (2026-09-02)</option>
                        <option value="d-m-Y" {{ (old('date_format', $companySettings['date_format'] ?? '') === 'd-m-Y') ? 'selected' : '' }}>DD-MM-YYYY (02-09-2026)</option>
                        <option value="d M, Y" {{ (old('date_format', $companySettings['date_format'] ?? '') === 'd M, Y') ? 'selected' : '' }}>DD Mon, YYYY (02 Sep, 2026)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tax / GST Identification Number</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $companySettings['tax_number'] ?? '07AAAAA0000A1Z5') }}">
                </div>
            </div>

            <hr class="my-6">

            <!-- Section 3: Operational Policies -->
            <h5 class="card-title mb-4">Standard Working Policies</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label required">Daily Work Hours</label>
                    <input type="number" step="0.5" name="working_hours_per_day" class="form-control" value="{{ old('working_hours_per_day', $companySettings['working_hours_per_day'] ?? 8.0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Weekly Work Days</label>
                    <input type="number" name="working_days_per_week" class="form-control" value="{{ old('working_days_per_week', $companySettings['working_days_per_week'] ?? 5) }}" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-6 pt-4 border-top">
                <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-device-floppy me-1"></i> Save Company Settings</button>
            </div>
        </form>
    </div>
@endsection

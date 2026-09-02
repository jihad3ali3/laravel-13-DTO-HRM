@extends('layouts.admin-layout')

@section('title', 'Holiday Calendar')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Company Holidays ({{ $selectedYear }})</h4>
            <p class="mb-0 text-muted">Manage official company holidays, public celebrations, and regional events</p>
        </div>
        @can('holidays.manage')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHolidayModal">
                <i class="icon-base ti tabler-calendar-plus me-1"></i> Add Holiday
            </button>
        @endcan
    </div>

    <!-- Year Filter & Calendar Cards -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::holidays.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 fw-medium">Select Year:</label>
                </div>
                <div class="col-auto">
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Holidays Grid -->
    <div class="row g-4">
        @forelse($holidays as $holiday)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 card-border-shadow-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-calendar-heart"></i></span>
                            </div>
                            <span class="badge bg-label-{{ $holiday->type === 'public' ? 'primary' : 'secondary' }} text-capitalize">
                                {{ $holiday->type }}
                            </span>
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $holiday->name }}</h5>
                        <p class="text-muted small mb-3">{{ $holiday->description ?? 'Official Holiday' }}</p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="fw-medium text-heading">
                                <i class="icon-base ti tabler-calendar me-1 text-danger"></i>
                                {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('D, M d, Y') }}
                            </span>
                            @can('holidays.manage')
                                <form method="POST" action="{{ route('admin::holidays.destroy', $holiday->id) }}" onsubmit="return confirm('Delete this holiday?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="icon-base ti tabler-calendar-off icon-48px text-muted mb-2"></i>
                    <h6>No holidays recorded for year {{ $selectedYear }}.</h6>
                </div>
            </div>
        @endforelse
    </div>

    @if($holidays->hasPages())
        <div class="d-flex justify-content-end mb-6">
            {{ $holidays->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Create Holiday Modal -->
    <div class="modal fade" id="createHolidayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::holidays.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Official Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Holiday Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Independence Day" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Date</label>
                        <input type="date" name="holiday_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Holiday Year</label>
                        <input type="number" name="holiday_year" class="form-control" value="{{ $selectedYear }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Category</label>
                        <select name="type" class="form-select" required>
                            <option value="public">Public / National Holiday</option>
                            <option value="company">Company / Optional Holiday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Add Holiday</button>
                </div>
            </form>
        </div>
    </div>
@endsection

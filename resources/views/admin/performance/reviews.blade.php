@extends('layouts.admin-layout')

@section('title', 'Appraisals & Reviews')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Appraisals &amp; Performance Reviews</h4>
            <p class="mb-0 text-muted">Conduct quarterly appraisals, evaluate competencies, and provide actionable feedback</p>
        </div>
        @can('performance.review')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReviewModal">
                <i class="icon-base ti tabler-star me-1"></i> New Performance Review
            </button>
        @endcan
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $reviews->total() }}</h4>
                        <small class="text-muted">Total Appraisals Conducted</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-certificate"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $avgRating }} / 5.0</h4>
                        <small class="text-muted">Average Workforce Rating</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-star-filled"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $employees->count() }}</h4>
                        <small class="text-muted">Active Eligible Staff</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-users"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Review Period</th>
                        <th>Rating</th>
                        <th>Reviewer</th>
                        <th>Key Strengths &amp; Feedback</th>
                        <th>Review Date</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($reviews as $rev)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($rev->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $rev->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $rev->employee->first_name ?? 'Employee' }} {{ $rev->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $rev->employee->department->name ?? 'General' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info text-capitalize">{{ str_replace('_', ' ', $rev->review_period) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">{{ number_format($rev->rating, 1) }}</span>
                                    <div class="text-warning small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="icon-base ti tabler-star{{ $i <= round($rev->rating) ? '-filled' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="small text-heading">{{ $rev->reviewer->name ?? 'HR Manager' }}</span>
                            </td>
                            <td>
                                <span class="small text-muted d-block">{{ \Illuminate\Support\Str::limit($rev->feedback ?? 'Comprehensive review conducted.', 50) }}</span>
                                @if($rev->strengths)
                                    <small class="text-success"><i class="icon-base ti tabler-thumb-up me-1"></i> {{ \Illuminate\Support\Str::limit($rev->strengths, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $rev->review_date ? \Carbon\Carbon::parse($rev->review_date)->format('M d, Y') : $rev->created_at->format('M d, Y') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No performance reviews recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- Create Review Modal -->
    <div class="modal fade" id="createReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('admin::performance.reviews.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Performance Appraisal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Review Period</label>
                            <select name="review_period" class="form-select" required>
                                <option value="Q1">Q1 (Jan - Mar)</option>
                                <option value="Q2">Q2 (Apr - Jun)</option>
                                <option value="Q3">Q3 (Jul - Sep)</option>
                                <option value="Q4">Q4 (Oct - Dec)</option>
                                <option value="annual">Annual Appraisal</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Overall Rating (1.0 to 5.0)</label>
                            <input type="number" step="0.1" min="1.0" max="5.0" name="rating" class="form-control" placeholder="4.5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Review Date</label>
                            <input type="date" name="review_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Feedback &amp; Assessment</label>
                        <textarea name="feedback" rows="3" class="form-control" placeholder="Detailed evaluation of performance..." required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Key Strengths</label>
                            <textarea name="strengths" rows="2" class="form-control" placeholder="Outstanding competencies..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Areas for Improvement</label>
                            <textarea name="areas_for_improvement" rows="2" class="form-control" placeholder="Growth targets..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Save Appraisal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

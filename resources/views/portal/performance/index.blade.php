@extends('layouts.admin-layout')

@section('title', 'My Goals & Performance Reviews')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">My Performance OKRs &amp; Appraisals</h4>
            <p class="mb-0 text-muted">Track progress towards key deliverables and view manager review evaluations</p>
        </div>
    </div>

    <!-- Goals Tracking Cards -->
    <div class="row g-4 mb-6">
        @forelse($goals as $g)
            <div class="col-md-6">
                <div class="card h-100 card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-1 text-heading">{{ $g->title }}</h5>
                            <span class="badge bg-label-{{ $g->status === 'completed' ? 'success' : 'primary' }} text-capitalize">{{ str_replace('_', ' ', $g->status) }}</span>
                        </div>
                        <p class="text-muted small mb-4">{{ $g->description }}</p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-medium">Progress Achieved</span>
                                <span class="small fw-bold text-primary">{{ $g->progress }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ $g->progress }}%"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <small class="text-muted">Due: {{ $g->due_date ? \Carbon\Carbon::parse($g->due_date)->format('M d, Y') : 'End of Quarter' }}</small>
                            <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#updateGoalModal{{ $g->id }}">
                                <i class="icon-base ti tabler-adjustments me-1"></i> Update My Progress
                            </button>
                        </div>

                        <!-- Update Goal Modal -->
                        <div class="modal fade" id="updateGoalModal{{ $g->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <form method="POST" action="{{ route('portal::performance.updateProgress', $g->id) }}" class="modal-content">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update OKR Progress</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label required">Self-Reported Progress (0-100%)</label>
                                            <input type="number" min="0" max="100" name="progress" class="form-control" value="{{ $g->progress }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="not_started" {{ $g->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                                <option value="in_progress" {{ $g->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $g->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Progress</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card p-5 text-center text-muted">No goals currently assigned.</div></div>
        @endforelse
    </div>

    <!-- Manager Appraisals Section -->
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Performance Appraisals &amp; Manager Feedback</h5></div>
        <div class="card-body">
            @forelse($reviews as $rev)
                <div class="p-4 bg-label-secondary rounded mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary text-uppercase">{{ $rev->review_cycle }}</span>
                        <div class="d-flex align-items-center text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="icon-base ti tabler-star{{ $i <= round($rev->rating) ? '-filled' : '' }}"></i>
                            @endfor
                            <span class="ms-2 fw-bold text-heading">({{ $rev->rating }}/5)</span>
                        </div>
                    </div>
                    <p class="text-heading mb-2"><strong>Manager Evaluation:</strong> {{ $rev->feedback }}</p>
                    @if($rev->strengths)
                        <p class="text-success mb-1 small"><i class="icon-base ti tabler-thumb-up me-1"></i> <strong>Strengths:</strong> {{ $rev->strengths }}</p>
                    @endif
                    @if($rev->area_of_improvements)
                        <p class="text-warning mb-0 small"><i class="icon-base ti tabler-arrow-up-right me-1"></i> <strong>Growth Opportunity:</strong> {{ $rev->area_of_improvements }}</p>
                    @endif
                </div>
            @empty
                <p class="text-muted text-center py-4 mb-0">No past appraisal evaluations found.</p>
            @endforelse
        </div>
    </div>
@endsection

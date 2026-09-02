@extends('layouts.admin-layout')

@section('title', 'Company Notice Board')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Company Notice Board &amp; Announcements</h4>
            <p class="mb-0 text-muted">Broadcast circulars, policy updates, company milestones, and event alerts</p>
        </div>
        @can('announcements.manage')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoticeModal">
                <i class="icon-base ti tabler-speakerphone me-1"></i> Post Announcement
            </button>
        @endcan
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $totalCount }}</h4>
                        <small class="text-muted">Total Published Notices</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-news"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-danger">{{ $pinnedCount }}</h4>
                        <small class="text-muted">Pinned Important Bulletins</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-pinned"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Feed / Grid -->
    <div class="row g-4">
        @forelse($announcements as $ann)
            <div class="col-md-6">
                <div class="card h-100 {{ $ann->is_pinned ? 'border-primary border-2 shadow-sm' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                @if($ann->is_pinned)
                                    <span class="badge bg-label-danger"><i class="icon-base ti tabler-pinned me-1"></i> Pinned</span>
                                @endif
                                <span class="badge bg-label-{{ $ann->priority === 'high' ? 'danger' : ($ann->priority === 'medium' ? 'warning' : 'info') }} text-uppercase">
                                    {{ $ann->priority }}
                                </span>
                            </div>
                            @can('announcements.manage')
                                <form method="POST" action="{{ route('admin::announcements.destroy', $ann->id) }}" onsubmit="return confirm('Delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>

                        <h5 class="card-title fw-bold mb-2">{{ $ann->title }}</h5>
                        <p class="text-muted small mb-4" style="min-height: 48px;">
                            {{ $ann->content }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center text-muted small border-top pt-3">
                            <span>
                                <i class="icon-base ti tabler-building me-1"></i>
                                {{ $ann->department ? $ann->department->name : 'All Company' }}
                            </span>
                            <span>
                                <i class="icon-base ti tabler-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($ann->start_date)->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center text-muted">
                    <i class="icon-base ti tabler-speakerphone-off icon-48px text-muted mb-2"></i>
                    <h6>No notices published yet.</h6>
                </div>
            </div>
        @endforelse
    </div>

    @if(method_exists($announcements, 'hasPages') && $announcements->hasPages())
        <div class="d-flex justify-content-end py-4">
            {{ $announcements->links() }}
        </div>
    @endif

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createNoticeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('admin::announcements.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Publish Company Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Annual Company Offsite 2026" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Target Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Company (General Broadcast)</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Priority Level</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low Priority (General Info)</option>
                                <option value="medium" selected>Medium Priority (Standard Notice)</option>
                                <option value="high">High Priority (Urgent Action Required)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End / Expiry Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Announcement Content</label>
                        <textarea name="content" rows="4" class="form-control" placeholder="Detailed message for staff..." required></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="pinSwitch">
                        <label class="form-check-label fw-medium" for="pinSwitch">Pin this notice to top of Dashboard</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-send me-1"></i> Broadcast Announcement</button>
                </div>
            </form>
        </div>
    </div>
@endsection

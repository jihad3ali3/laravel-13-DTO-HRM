@extends('layouts.admin-layout')

@section('title', 'Goals & OKRs')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Goals &amp; Performance OKRs</h4>
            <p class="mb-0 text-muted">Set quarterly objectives, key results, and monitor progress trajectories</p>
        </div>
        @can('performance.manage')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGoalModal">
                <i class="icon-base ti tabler-target-arrow me-1"></i> Add Goal / OKR
            </button>
        @endcan
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-6">
        <div class="col-sm-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-info">{{ $inProgressCount }}</h4>
                        <small class="text-muted">Goals In Progress</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-clock-play"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-success">{{ $completedCount }}</h4>
                        <small class="text-muted">Completed OKRs</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-circle-check"></i></span></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $notStartedCount }}</h4>
                        <small class="text-muted">Upcoming / Pending Start</small>
                    </div>
                    <div class="avatar"><span class="avatar-initial rounded bg-label-secondary"><i class="icon-base ti tabler-hourglass-empty"></i></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::performance.goals') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::performance.goals') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Goals Grid / Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Goal Title &amp; Target</th>
                        <th>Progress</th>
                        <th>Target Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($goals as $goal)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($goal->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $goal->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $goal->employee->first_name ?? 'Employee' }} {{ $goal->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $goal->employee->department->name ?? 'General' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 small fw-bold">{{ $goal->title }}</h6>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($goal->description ?? 'No description', 40) }}</small>
                            </td>
                            <td style="min-width: 160px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold">{{ $goal->progress }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $goal->progress >= 100 ? 'success' : ($goal->progress > 40 ? 'primary' : 'warning') }}" style="width: {{ $goal->progress }}%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $goal->end_date ? \Carbon\Carbon::parse($goal->end_date)->format('M d, Y') : 'Open Ended' }}</span>
                            </td>
                            <td>
                                @if($goal->status === 'completed')
                                    <span class="badge bg-label-success">Completed</span>
                                @elseif($goal->status === 'in_progress')
                                    <span class="badge bg-label-primary">In Progress</span>
                                @else
                                    <span class="badge bg-label-secondary">Not Started</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#progressModal{{ $goal->id }}" title="Update Progress">
                                        <i class="icon-base ti tabler-adjustments"></i>
                                    </button>
                                    @can('performance.manage')
                                        <form method="POST" action="{{ route('admin::performance.goals.destroy', $goal->id) }}" onsubmit="return confirm('Delete this goal?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete Goal">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>

                                <!-- Progress Update Modal -->
                                <div class="modal fade" id="progressModal{{ $goal->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm text-start">
                                        <form method="POST" action="{{ route('admin::performance.goals.progress', $goal->id) }}" class="modal-content">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Progress</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label required">Progress Percentage (0 - 100%)</label>
                                                    <input type="number" min="0" max="100" name="progress" class="form-control" value="{{ $goal->progress }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label required">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="not_started" {{ $goal->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                                        <option value="in_progress" {{ $goal->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="completed" {{ $goal->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No performance goals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($goals->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $goals->links() }}
            </div>
        @endif
    </div>

    <!-- Create Goal Modal -->
    <div class="modal fade" id="createGoalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::performance.goals.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Set Performance Goal / OKR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Goal Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Lead migration of authentication module" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">Target End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+3 months')) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Goal Description &amp; Metrics</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Specific deliverables and expectations..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Establish Goal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

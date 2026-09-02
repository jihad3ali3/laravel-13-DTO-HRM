@extends('layouts.admin-layout')

@section('title', 'Departments')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Organizational Departments</h4>
            <p class="mb-0 text-muted">Manage company divisions, codes, and team allocations</p>
        </div>
        @can('settings.manage')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeptModal">
                <i class="icon-base ti tabler-plus me-1"></i> Add Department
            </button>
        @endcan
    </div>

    <!-- Departments Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Headcount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($departments as $dept)
                        <tr>
                            <td><span class="fw-bold text-heading">{{ $dept->name }}</span></td>
                            <td><span class="badge bg-label-primary">{{ $dept->code }}</span></td>
                            <td><small class="text-muted">{{ $dept->description ?? 'Standard Division' }}</small></td>
                            <td><span class="badge bg-label-info">{{ $dept->employees_count ?? 0 }} Staff</span></td>
                            <td>
                                @if($dept->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('settings.manage')
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}" title="Edit">
                                            <i class="icon-base ti tabler-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin::settings.departments.destroy', $dept->id) }}" onsubmit="return confirm('Delete this department?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <form method="POST" action="{{ route('admin::settings.departments.update', $dept->id) }}" class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Department</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label required">Department Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label required">Code</label>
                                                        <input type="text" name="code" class="form-control" value="{{ $dept->code }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $dept->description }}</textarea>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act{{ $dept->id }}" {{ $dept->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="act{{ $dept->id }}">Active Department</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No departments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($departments->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $departments->links() }}
            </div>
        @endif
    </div>

    <!-- Create Department Modal -->
    <div class="modal fade" id="createDeptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::settings.departments.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Department Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Product Engineering" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Department Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. ENG" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional description..."></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createAct" checked>
                        <label class="form-check-label" for="createAct">Active Department</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Create Department</button>
                </div>
            </form>
        </div>
    </div>
@endsection

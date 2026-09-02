@extends('layouts.admin-layout')

@section('title', 'Designations & Job Titles')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Designations &amp; Job Titles</h4>
            <p class="mb-0 text-muted">Manage job roles, department affiliations, and responsibilities</p>
        </div>
        @can('settings.manage')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDesigModal">
                <i class="icon-base ti tabler-plus me-1"></i> Add Designation
            </button>
        @endcan
    </div>

    <!-- Designations Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Department</th>
                        <th>Description</th>
                        <th>Total Staff</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($designations as $desig)
                        <tr>
                            <td><span class="fw-bold text-heading">{{ $desig->title }}</span></td>
                            <td><span class="badge bg-label-primary">{{ $desig->department->name ?? 'General' }}</span></td>
                            <td><small class="text-muted">{{ $desig->description ?? 'Standard Title' }}</small></td>
                            <td><span class="badge bg-label-info">{{ $desig->employees->count() }} Staff</span></td>
                            <td>
                                @if($desig->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('settings.manage')
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#editDesigModal{{ $desig->id }}" title="Edit">
                                            <i class="icon-base ti tabler-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin::settings.designations.destroy', $desig->id) }}" onsubmit="return confirm('Delete this designation?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editDesigModal{{ $desig->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <form method="POST" action="{{ route('admin::settings.designations.update', $desig->id) }}" class="modal-content">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Designation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label required">Designation Title</label>
                                                        <input type="text" name="title" class="form-control" value="{{ $desig->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label required">Department</label>
                                                        <select name="department_id" class="form-select" required>
                                                            @foreach($departments as $dept)
                                                                <option value="{{ $dept->id }}" {{ $desig->department_id == $dept->id ? 'selected' : '' }}>
                                                                    {{ $dept->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $desig->description }}</textarea>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="actD{{ $desig->id }}" {{ $desig->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="actD{{ $desig->id }}">Active Title</label>
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
                        <tr><td colspan="6" class="text-center py-5 text-muted">No designations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($designations->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $designations->links() }}
            </div>
        @endif
    </div>

    <!-- Create Designation Modal -->
    <div class="modal fade" id="createDesigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::settings.designations.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Designation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Designation Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Senior Software Engineer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Department</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Choose Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Key responsibilities..."></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createDesigAct" checked>
                        <label class="form-check-label" for="createDesigAct">Active Title</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Add Designation</button>
                </div>
            </form>
        </div>
    </div>
@endsection

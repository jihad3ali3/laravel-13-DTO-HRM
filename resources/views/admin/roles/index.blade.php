@extends('layouts.admin-layout')

@section('title', 'Roles & Access Control')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Roles &amp; Access Control</h4>
            <p class="mb-0 text-muted">Manage system security roles, permission assignments, and user role allocation</p>
        </div>
        @can('roles.manage')
            <div class="d-flex gap-2">
                <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#assignUserRoleModal">
                    <i class="icon-base ti tabler-user-check me-1"></i> Assign User Role
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                    <i class="icon-base ti tabler-shield-plus me-1"></i> Add New Role
                </button>
            </div>
        @endcan
    </div>

    <!-- Roles Grid -->
    <div class="row g-4 mb-6">
        @forelse($roles as $role)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-label-primary fs-6 text-capitalize">{{ str_replace('_', ' ', $role->name) }}</span>
                            <span class="badge bg-label-secondary">{{ $role->users->count() }} Users</span>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $role->permissions->count() }} Total Permissions</h5>
                        <p class="text-muted small mb-4">Guard: <code>{{ $role->guard_name }}</code></p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="{{ route('admin::roles.permissions') }}" class="small fw-medium text-primary">
                                <i class="icon-base ti tabler-shield-lock me-1"></i> View Matrix
                            </a>
                            @can('roles.manage')
                                @if(!in_array($role->name, ['admin', 'employee', 'hr_manager']))
                                    <form method="POST" action="{{ route('admin::roles.destroy', $role->id) }}" onsubmit="return confirm('Delete this role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card p-5 text-center text-muted">No roles found.</div></div>
        @endforelse
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form method="POST" action="{{ route('admin::roles.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Security Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Role Name (Identifier)</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. payroll_officer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Select Allowed Permissions</label>
                        <div class="row g-2" style="max-height: 300px; overflow-y: auto;">
                            @foreach($permissions as $perm)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="p_{{ $perm->id }}">
                                        <label class="form-check-label small" for="p_{{ $perm->id }}">
                                            {{ $perm->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Create Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign User Role Modal -->
    <div class="modal fade" id="assignUserRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::roles.assignUser') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Role to User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Select User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose User</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Assign Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">Choose Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ ucfirst(str_replace('_', ' ', $r->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-check me-1"></i> Assign Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection

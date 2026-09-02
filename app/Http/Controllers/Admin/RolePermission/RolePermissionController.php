<?php

namespace App\Http\Controllers\Admin\RolePermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RolePermission\RoleAssignRequest;
use App\Http\Requests\Api\V1\RolePermission\RoleStoreRequest;
use App\Http\Services\RolePermissionService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __construct(private readonly RolePermissionService $rolePermissionService) {}

    public function index(): View
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all();
        $users = User::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions', 'users'));
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->rolePermissionService->store($request->toDto());

        return redirect()->back()->with('success', 'Security role created successfully.');
    }

    public function update(int $id, RoleStoreRequest $request): RedirectResponse
    {
        $this->rolePermissionService->update($id, $request->toDto());

        return redirect()->back()->with('success', 'Security role updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->rolePermissionService->destroy($id);

        return redirect()->back()->with('success', 'Security role deleted successfully.');
    }

    public function permissions(): View
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0] ?? 'general';
        });

        return view('admin.roles.permissions', compact('roles', 'permissions'));
    }

    public function assignUser(RoleAssignRequest $request): RedirectResponse
    {
        $this->rolePermissionService->assignUserRole($request->toDto());

        return redirect()->back()->with('success', 'Role assigned to user successfully.');
    }
}

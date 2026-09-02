<?php

namespace App\Repositories;

use App\Domain\RolePermission\RoleAssignDto;
use App\Domain\RolePermission\RoleStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RolePermissionRepository
{
    public function getPermissions()
    {
        return Permission::all();
    }

    /**
     * @throws RepositoryException
     */
    public function assignUserRole(RoleAssignDto $dto): User
    {
        $user = User::find($dto->userId);
        if (! $user) {
            throw new RepositoryException('User not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $user->syncRoles([$dto->roleName]);

        return $user->load('roles');
    }

    public function getRoles()
    {
        return Role::with('permissions')->get();
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Role
    {
        $role = Role::with('permissions')->find($id);
        if (! $role) {
            throw new RepositoryException('Role not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $role;
    }

    public function store(RoleStoreDto $dto): Role
    {
        $role = Role::create(['name' => $dto->name]);
        if (! empty($dto->permissions)) {
            $role->syncPermissions($dto->permissions);
        }

        return $role->load('permissions');
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, RoleStoreDto $dto): Role
    {
        $role = Role::find($id);
        if (! $role) {
            throw new RepositoryException('Role not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $role->update(['name' => $dto->name]);
        $role->syncPermissions($dto->permissions);

        return $role->load('permissions');
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        $role = Role::find($id);
        if (! $role) {
            throw new RepositoryException('Role not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $role->delete();
    }
}

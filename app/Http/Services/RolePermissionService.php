<?php

namespace App\Http\Services;

use App\Domain\RolePermission\RoleAssignDto;
use App\Domain\RolePermission\RoleStoreDto;
use App\Repositories\RolePermissionRepository;

readonly class RolePermissionService
{
    public function __construct(private RolePermissionRepository $rolePermissionRepository) {}

    public function getPermissions()
    {
        return $this->rolePermissionRepository->getPermissions();
    }

    public function assignUserRole(RoleAssignDto $dto)
    {
        return $this->rolePermissionRepository->assignUserRole($dto);
    }

    public function getRoles()
    {
        return $this->rolePermissionRepository->getRoles();
    }

    public function show(int $id)
    {
        return $this->rolePermissionRepository->show($id);
    }

    public function store(RoleStoreDto $dto)
    {
        return $this->rolePermissionRepository->store($dto);
    }

    public function update(int $id, RoleStoreDto $dto)
    {
        return $this->rolePermissionRepository->update($id, $dto);
    }

    public function destroy(int $id): void
    {
        $this->rolePermissionRepository->destroy($id);
    }
}

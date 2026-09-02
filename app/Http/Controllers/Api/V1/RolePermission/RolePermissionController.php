<?php

namespace App\Http\Controllers\Api\V1\RolePermission;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RolePermission\RoleAssignRequest;
use App\Http\Requests\Api\V1\RolePermission\RoleStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;

class RolePermissionController extends Controller
{
    public function __construct(private readonly RolePermissionService $rolePermissionService) {}

    public function permissions(): JsonResponse
    {
        $permissions = $this->rolePermissionService->getPermissions();

        return ApiResponse::success($permissions, 'Permissions list retrieved successfully.');
    }

    public function assignUserRole(RoleAssignRequest $request): JsonResponse
    {
        try {
            $user = $this->rolePermissionService->assignUserRole($request->toDto());

            return ApiResponse::success($user, 'User role updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function index(): JsonResponse
    {
        $roles = $this->rolePermissionService->getRoles();

        return ApiResponse::success($roles, 'Roles and permissions retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->rolePermissionService->show($id);

            return ApiResponse::success($role, 'Role details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(RoleStoreRequest $request): JsonResponse
    {
        $role = $this->rolePermissionService->store($request->toDto());

        return ApiResponse::created($role, 'Role created successfully.');
    }

    public function update(int $id, RoleStoreRequest $request): JsonResponse
    {
        try {
            $role = $this->rolePermissionService->update($id, $request->toDto());

            return ApiResponse::success($role, 'Role updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->rolePermissionService->destroy($id);

            return ApiResponse::success(null, 'Role deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}

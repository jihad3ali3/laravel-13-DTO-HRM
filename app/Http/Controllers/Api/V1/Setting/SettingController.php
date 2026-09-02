<?php

namespace App\Http\Controllers\Api\V1\Setting;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\CompanySettingUpdateRequest;
use App\Http\Requests\Api\V1\Setting\DepartmentStoreRequest;
use App\Http\Requests\Api\V1\Setting\DesignationStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService) {}

    public function departments(): JsonResponse
    {
        $departments = $this->settingService->getDepartments();

        return ApiResponse::success($departments, 'Departments retrieved successfully.');
    }

    public function storeDepartment(DepartmentStoreRequest $request): JsonResponse
    {
        $department = $this->settingService->storeDepartment($request->toDto());

        return ApiResponse::created($department, 'Department created successfully.');
    }

    public function updateDepartment(int $id, DepartmentStoreRequest $request): JsonResponse
    {
        try {
            $department = $this->settingService->updateDepartment($id, $request->toDto());

            return ApiResponse::success($department, 'Department updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroyDepartment(int $id): JsonResponse
    {
        try {
            $this->settingService->destroyDepartment($id);

            return ApiResponse::success(null, 'Department deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function designations(): JsonResponse
    {
        $designations = $this->settingService->getDesignations();

        return ApiResponse::success($designations, 'Designations retrieved successfully.');
    }

    public function storeDesignation(DesignationStoreRequest $request): JsonResponse
    {
        $designation = $this->settingService->storeDesignation($request->toDto());

        return ApiResponse::created($designation, 'Designation created successfully.');
    }

    public function updateDesignation(int $id, DesignationStoreRequest $request): JsonResponse
    {
        try {
            $designation = $this->settingService->updateDesignation($id, $request->toDto());

            return ApiResponse::success($designation, 'Designation updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function destroyDesignation(int $id): JsonResponse
    {
        try {
            $this->settingService->destroyDesignation($id);

            return ApiResponse::success(null, 'Designation deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function getCompanySettings(): JsonResponse
    {
        $settings = $this->settingService->getCompanySettings();

        return ApiResponse::success($settings, 'Company settings retrieved successfully.');
    }

    public function updateCompanySettings(CompanySettingUpdateRequest $request): JsonResponse
    {
        $settings = $this->settingService->updateCompanySettings($request->toDto());

        return ApiResponse::success($settings, 'Company settings updated successfully.');
    }
}

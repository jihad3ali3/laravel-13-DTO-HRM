<?php

namespace App\Repositories;

use App\Domain\Setting\CompanySettingUpdateDto;
use App\Domain\Setting\DepartmentStoreDto;
use App\Domain\Setting\DesignationStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\CompanySetting;
use App\Models\Department;
use App\Models\Designation;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SettingRepository
{
    public function getDepartments()
    {
        return Department::query()->latest()->get();
    }

    public function storeDepartment(DepartmentStoreDto $dto): Department
    {
        return Department::create([
            'name' => $dto->name,
            'code' => $dto->code,
            'description' => $dto->description,
            'is_active' => $dto->isActive,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function updateDepartment(int $id, DepartmentStoreDto $dto): Department
    {
        $department = Department::find($id);
        if (! $department) {
            throw new RepositoryException('Department not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $department->update([
            'name' => $dto->name,
            'code' => $dto->code,
            'description' => $dto->description,
            'is_active' => $dto->isActive,
        ]);

        return $department;
    }

    /**
     * @throws RepositoryException
     */
    public function destroyDepartment(int $id): void
    {
        $department = Department::find($id);
        if (! $department) {
            throw new RepositoryException('Department not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $department->delete();
    }

    public function getDesignations()
    {
        return Designation::with('department')->latest()->get();
    }

    public function storeDesignation(DesignationStoreDto $dto): Designation
    {
        return Designation::create([
            'department_id' => $dto->departmentId,
            'name' => $dto->name,
            'description' => $dto->description,
            'is_active' => $dto->isActive,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function updateDesignation(int $id, DesignationStoreDto $dto): Designation
    {
        $designation = Designation::find($id);
        if (! $designation) {
            throw new RepositoryException('Designation not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $designation->update([
            'department_id' => $dto->departmentId,
            'name' => $dto->name,
            'description' => $dto->description,
            'is_active' => $dto->isActive,
        ]);

        return $designation;
    }

    /**
     * @throws RepositoryException
     */
    public function destroyDesignation(int $id): void
    {
        $designation = Designation::find($id);
        if (! $designation) {
            throw new RepositoryException('Designation not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $designation->delete();
    }

    public function getCompanySettings(): array
    {
        return CompanySetting::all()->pluck('value', 'key')->toArray();
    }

    public function updateCompanySettings(CompanySettingUpdateDto $dto): array
    {
        foreach ($dto->settings as $key => $value) {
            CompanySetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
        }

        return $this->getCompanySettings();
    }
}

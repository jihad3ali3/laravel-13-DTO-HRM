<?php

namespace App\Http\Services;

use App\Domain\Setting\CompanySettingUpdateDto;
use App\Domain\Setting\DepartmentStoreDto;
use App\Domain\Setting\DesignationStoreDto;
use App\Repositories\SettingRepository;

readonly class SettingService
{
    public function __construct(private SettingRepository $settingRepository) {}

    public function getDepartments()
    {
        return $this->settingRepository->getDepartments();
    }

    public function storeDepartment(DepartmentStoreDto $dto)
    {
        return $this->settingRepository->storeDepartment($dto);
    }

    public function updateDepartment(int $id, DepartmentStoreDto $dto)
    {
        return $this->settingRepository->updateDepartment($id, $dto);
    }

    public function destroyDepartment(int $id): void
    {
        $this->settingRepository->destroyDepartment($id);
    }

    public function getDesignations()
    {
        return $this->settingRepository->getDesignations();
    }

    public function storeDesignation(DesignationStoreDto $dto)
    {
        return $this->settingRepository->storeDesignation($dto);
    }

    public function updateDesignation(int $id, DesignationStoreDto $dto)
    {
        return $this->settingRepository->updateDesignation($id, $dto);
    }

    public function destroyDesignation(int $id): void
    {
        $this->settingRepository->destroyDesignation($id);
    }

    public function getCompanySettings(): array
    {
        return $this->settingRepository->getCompanySettings();
    }

    public function updateCompanySettings(CompanySettingUpdateDto $dto): array
    {
        return $this->settingRepository->updateCompanySettings($dto);
    }
}

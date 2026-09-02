<?php

namespace App\Http\Services;

use App\Domain\Shift\ShiftAssignDto;
use App\Domain\Shift\ShiftStoreDto;
use App\Repositories\ShiftRepository;

readonly class ShiftService
{
    public function __construct(private ShiftRepository $shiftRepository) {}

    public function getEmployeeShifts(int $employeeId)
    {
        return $this->shiftRepository->getEmployeeShifts($employeeId);
    }

    public function getShifts()
    {
        return $this->shiftRepository->getShifts();
    }

    public function show(int $id)
    {
        return $this->shiftRepository->show($id);
    }

    public function store(ShiftStoreDto $dto)
    {
        return $this->shiftRepository->store($dto);
    }

    public function update(int $id, ShiftStoreDto $dto)
    {
        return $this->shiftRepository->update($id, $dto);
    }

    public function destroy(int $id): void
    {
        $this->shiftRepository->destroy($id);
    }

    public function assign(ShiftAssignDto $dto)
    {
        return $this->shiftRepository->assign($dto);
    }
}

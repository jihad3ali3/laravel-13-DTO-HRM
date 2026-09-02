<?php

namespace App\Repositories;

use App\Domain\Shift\ShiftAssignDto;
use App\Domain\Shift\ShiftStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ShiftRepository
{
    public function getEmployeeShifts(int $employeeId)
    {
        return EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->latest()
            ->get();
    }

    public function getShifts()
    {
        return Shift::query()->latest()->get();
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Shift
    {
        $shift = Shift::find($id);
        if (! $shift) {
            throw new RepositoryException('Shift not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $shift;
    }

    public function store(ShiftStoreDto $dto): Shift
    {
        return Shift::create([
            'name' => $dto->name,
            'shift_code' => $dto->shiftCode,
            'start_time' => $dto->startTime,
            'end_time' => $dto->endTime,
            'mark_late_after_minutes' => $dto->markLateAfterMinutes,
            'total_hours' => $dto->totalHours,
            'is_active' => $dto->isActive,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, ShiftStoreDto $dto): Shift
    {
        $shift = Shift::find($id);
        if (! $shift) {
            throw new RepositoryException('Shift not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $shift->update([
            'name' => $dto->name,
            'shift_code' => $dto->shiftCode,
            'start_time' => $dto->startTime,
            'end_time' => $dto->endTime,
            'mark_late_after_minutes' => $dto->markLateAfterMinutes,
            'total_hours' => $dto->totalHours,
            'is_active' => $dto->isActive,
        ]);

        return $shift;
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        $shift = Shift::find($id);
        if (! $shift) {
            throw new RepositoryException('Shift not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $shift->delete();
    }

    public function assign(ShiftAssignDto $dto): EmployeeShift
    {
        EmployeeShift::where('employee_id', $dto->employeeId)->update(['is_active' => false]);

        return EmployeeShift::create([
            'employee_id' => $dto->employeeId,
            'shift_id' => $dto->shiftId,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'is_active' => $dto->isActive,
        ]);
    }
}

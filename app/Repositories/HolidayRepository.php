<?php

namespace App\Repositories;

use App\Domain\Holiday\HolidayStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\Holiday;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HolidayRepository
{
    public function getHolidays()
    {
        return Holiday::query()->latest('holiday_date')->get();
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Holiday
    {
        $holiday = Holiday::find($id);
        if (! $holiday) {
            throw new RepositoryException('Holiday not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $holiday;
    }

    public function store(HolidayStoreDto $dto): Holiday
    {
        $year = Carbon::parse($dto->holidayDate)->year;

        return Holiday::create([
            'name' => $dto->name,
            'holiday_date' => $dto->holidayDate,
            'type' => $dto->type,
            'holiday_year' => $year,
            'description' => $dto->description,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, HolidayStoreDto $dto): Holiday
    {
        $holiday = Holiday::find($id);
        if (! $holiday) {
            throw new RepositoryException('Holiday not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $year = Carbon::parse($dto->holidayDate)->year;
        $holiday->update([
            'name' => $dto->name,
            'holiday_date' => $dto->holidayDate,
            'type' => $dto->type,
            'holiday_year' => $year,
            'description' => $dto->description,
        ]);

        return $holiday;
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        $holiday = Holiday::find($id);
        if (! $holiday) {
            throw new RepositoryException('Holiday not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $holiday->delete();
    }
}

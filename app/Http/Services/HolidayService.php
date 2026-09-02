<?php

namespace App\Http\Services;

use App\Domain\Holiday\HolidayStoreDto;
use App\Repositories\HolidayRepository;

readonly class HolidayService
{
    public function __construct(private HolidayRepository $holidayRepository) {}

    public function getHolidays()
    {
        return $this->holidayRepository->getHolidays();
    }

    public function show(int $id)
    {
        return $this->holidayRepository->show($id);
    }

    public function store(HolidayStoreDto $dto)
    {
        return $this->holidayRepository->store($dto);
    }

    public function update(int $id, HolidayStoreDto $dto)
    {
        return $this->holidayRepository->update($id, $dto);
    }

    public function destroy(int $id): void
    {
        $this->holidayRepository->destroy($id);
    }
}

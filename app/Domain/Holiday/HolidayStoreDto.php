<?php

namespace App\Domain\Holiday;

class HolidayStoreDto
{
    public function __construct(
        public string $name,
        public string $holidayDate,
        public string $type = 'public',
        public ?string $description = null
    ) {}
}

<?php

namespace App\Domain\Setting;

class DepartmentStoreDto
{
    public function __construct(
        public string $name,
        public string $code,
        public ?string $description = null,
        public bool $isActive = true
    ) {}
}

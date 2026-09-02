<?php

namespace App\Domain\Setting;

class DesignationStoreDto
{
    public function __construct(
        public int $departmentId,
        public string $name,
        public ?string $description = null,
        public bool $isActive = true
    ) {}
}

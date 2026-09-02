<?php

namespace App\Domain\RolePermission;

class RoleStoreDto
{
    public function __construct(
        public string $name,
        public array $permissions = []
    ) {}
}

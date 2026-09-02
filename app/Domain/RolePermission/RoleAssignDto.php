<?php

namespace App\Domain\RolePermission;

class RoleAssignDto
{
    public function __construct(
        public int $userId,
        public string $roleName
    ) {}
}

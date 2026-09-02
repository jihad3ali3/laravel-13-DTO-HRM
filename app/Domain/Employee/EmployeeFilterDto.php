<?php

namespace App\Domain\Employee;

class EmployeeFilterDto
{
    public function __construct(
        public ?string $search = null,
        public ?int $departmentId = null,
        public ?int $designationId = null,
        public int $perPage = 15
    ) {}
}

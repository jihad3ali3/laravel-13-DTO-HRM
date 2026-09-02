<?php

namespace App\Domain\Employee;

class EmployeeUpdateDto
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public ?string $phone,
        public string $gender,
        public ?string $dateOfBirth,
        public ?int $departmentId,
        public ?int $designationId,
        public string $employmentType,
        public float $salary,
        public ?string $address,
        public ?string $emergencyContact,
        public bool $isActive = true
    ) {}
}

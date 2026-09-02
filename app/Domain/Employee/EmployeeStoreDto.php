<?php

namespace App\Domain\Employee;

class EmployeeStoreDto
{
    public function __construct(
        public string $employeeCode,
        public string $firstName,
        public ?string $lastName,
        public string $email,
        public ?string $phone,
        public string $gender,
        public ?string $dateOfBirth,
        public string $joiningDate,
        public ?int $departmentId,
        public ?int $designationId,
        public string $employmentType,
        public float $salary,
        public ?string $avatar,
        public ?string $address,
        public ?string $emergencyContact,
        public string $password,
        public string $role = 'employee'
    ) {}
}

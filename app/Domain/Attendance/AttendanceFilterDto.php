<?php

declare(strict_types=1);

namespace App\Domain\Attendance;

readonly class AttendanceFilterDto
{
    public function __construct(
        public ?int $employeeId = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?int $departmentId = null,
        public ?string $status = null,
        public int $perPage = 100,
    ) {}

    public static function fromArray(array $data): AttendanceFilterDto
    {
        return new self(
            employeeId: ! empty($data['employee_id']) ? $data['employee_id'] : null,
            fromDate: ! empty($data['from_date']) ? $data['from_date'] : null,
            toDate: ! empty($data['to_date']) ? $data['to_date'] : null,
            departmentId: ! empty($data['department_id']) ? $data['department_id'] : null,
            status: ! empty($data['status']) ? $data['status'] : null,
            perPage: ! empty($data['per_page']) ? $data['per_page'] : 100,
        );
    }
}

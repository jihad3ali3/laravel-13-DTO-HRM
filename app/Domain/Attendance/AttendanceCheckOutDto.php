<?php

declare(strict_types=1);

namespace App\Domain\Attendance;

readonly class AttendanceCheckOutDto
{
    public function __construct(
        public ?int $employeeId = null
    ) {}

    public static function fromArray(array $data): AttendanceCheckOutDto
    {
        return new self(
            employeeId: ! empty($data['employee_id']) ? $data['employee_id'] : null,
        );
    }
}

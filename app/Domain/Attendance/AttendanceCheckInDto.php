<?php

declare(strict_types=1);

namespace App\Domain\Attendance;

readonly class AttendanceCheckInDto
{
    public function __construct(
        public ?int $employeeId
    ) {}

    public static function fromArray(array $data): AttendanceCheckInDto
    {
        return new self(
            employeeId: ! empty($data['employee_id']) ? $data['employee_id'] : null
        );
    }
}

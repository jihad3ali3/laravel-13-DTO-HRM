<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Domain\Attendance\AttendanceFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|integer|exists:employees,id',
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'department_id' => 'nullable|integer|exists:departments,id',
            'status' => 'nullable|string',
            'per_page' => 'nullable|integer|between:1,100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AttendanceFilterDto
    {
        return AttendanceFilterDto::fromArray($this->validated());
    }
}

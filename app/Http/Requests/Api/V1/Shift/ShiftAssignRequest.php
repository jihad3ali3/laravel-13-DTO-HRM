<?php

namespace App\Http\Requests\Api\V1\Shift;

use App\Domain\Shift\ShiftAssignDto;
use Illuminate\Foundation\Http\FormRequest;

class ShiftAssignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ShiftAssignDto
    {
        return new ShiftAssignDto(
            employeeId: (int) $this->input('employee_id'),
            shiftId: (int) $this->input('shift_id'),
            startDate: $this->input('start_date'),
            endDate: $this->input('end_date'),
            isActive: $this->boolean('is_active', true)
        );
    }
}

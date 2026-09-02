<?php

namespace App\Http\Requests\Api\V1\Shift;

use App\Domain\Shift\ShiftStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class ShiftStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'shift_code' => 'required|string|unique:shifts,shift_code,'.$this->route('id'),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'mark_late_after_minutes' => 'nullable|integer|min:0',
            'total_hours' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ShiftStoreDto
    {
        return new ShiftStoreDto(
            name: $this->input('name'),
            shiftCode: $this->input('shift_code'),
            startTime: $this->input('start_time'),
            endTime: $this->input('end_time'),
            markLateAfterMinutes: (int) $this->input('mark_late_after_minutes', 0),
            totalHours: (float) $this->input('total_hours', 0.0),
            isActive: $this->boolean('is_active', true)
        );
    }
}

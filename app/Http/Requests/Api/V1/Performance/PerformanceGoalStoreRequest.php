<?php

namespace App\Http\Requests\Api\V1\Performance;

use App\Domain\Performance\PerformanceGoalStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class PerformanceGoalStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|string|in:low,medium,high',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): PerformanceGoalStoreDto
    {
        return new PerformanceGoalStoreDto(
            employeeId: (int) $this->input('employee_id'),
            title: $this->input('title'),
            description: $this->input('description'),
            startDate: $this->input('start_date'),
            dueDate: $this->input('due_date'),
            priority: $this->input('priority')
        );
    }
}

<?php

namespace App\Http\Requests\Api\V1\Employee;

use App\Domain\Employee\EmployeeFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'per_page' => 'nullable|integer|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): EmployeeFilterDto
    {
        return new EmployeeFilterDto(
            search: $this->input('search'),
            departmentId: $this->input('department_id') ? (int) $this->input('department_id') : null,
            designationId: $this->input('designation_id') ? (int) $this->input('designation_id') : null,
            perPage: (int) $this->input('per_page', 15)
        );
    }
}

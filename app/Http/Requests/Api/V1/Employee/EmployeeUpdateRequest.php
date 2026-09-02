<?php

namespace App\Http\Requests\Api\V1\Employee;

use App\Domain\Employee\EmployeeUpdateDto;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'gender' => 'required|string|in:male,female,others',
            'date_of_birth' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'employment_type' => 'required|string|in:full_time,part_time,contract,intern',
            'salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): EmployeeUpdateDto
    {
        return new EmployeeUpdateDto(
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
            phone: $this->input('phone'),
            gender: $this->input('gender'),
            dateOfBirth: $this->input('date_of_birth'),
            departmentId: $this->input('department_id') ? (int) $this->input('department_id') : null,
            designationId: $this->input('designation_id') ? (int) $this->input('designation_id') : null,
            employmentType: $this->input('employment_type'),
            salary: (float) $this->input('salary'),
            address: $this->input('address'),
            emergencyContact: $this->input('emergency_contact'),
            isActive: $this->boolean('is_active', true)
        );
    }
}

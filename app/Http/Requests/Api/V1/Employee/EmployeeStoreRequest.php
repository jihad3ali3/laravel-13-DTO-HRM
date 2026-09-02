<?php

namespace App\Http\Requests\Api\V1\Employee;

use App\Domain\Employee\EmployeeStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_code' => 'required|string|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email|unique:employees,email',
            'phone' => 'nullable|string',
            'gender' => 'required|string|in:male,female,others',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'employment_type' => 'required|string|in:full_time,part_time,contract,intern',
            'salary' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): EmployeeStoreDto
    {
        $avatarPath = null;
        if ($this->hasFile('avatar')) {
            $avatarPath = $this->file('avatar')->store('avatars', 'public');
        }

        return new EmployeeStoreDto(
            employeeCode: $this->input('employee_code'),
            firstName: $this->input('first_name'),
            lastName: $this->input('last_name'),
            email: $this->input('email'),
            phone: $this->input('phone'),
            gender: $this->input('gender'),
            dateOfBirth: $this->input('date_of_birth'),
            joiningDate: $this->input('joining_date'),
            departmentId: $this->input('department_id') ? (int) $this->input('department_id') : null,
            designationId: $this->input('designation_id') ? (int) $this->input('designation_id') : null,
            employmentType: $this->input('employment_type'),
            salary: (float) $this->input('salary'),
            avatar: $avatarPath,
            address: $this->input('address'),
            emergencyContact: $this->input('emergency_contact'),
            password: $this->input('password'),
            role: $this->input('role', 'employee')
        );
    }
}

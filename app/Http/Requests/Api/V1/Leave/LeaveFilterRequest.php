<?php

namespace App\Http\Requests\Api\V1\Leave;

use App\Domain\Leave\LeaveFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class LeaveFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'per_page' => 'nullable|integer|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): LeaveFilterDto
    {
        $employeeId = $this->input('employee_id');
        $user = $this->user();
        if ($user && ! $user->hasAnyPermission(['leaves.approve', 'employees.edit']) && $user->employee) {
            $employeeId = $user->employee->id;
        }

        return new LeaveFilterDto(
            employeeId: $employeeId ? (int) $employeeId : null,
            status: $this->input('status'),
            perPage: (int) $this->input('per_page', 15)
        );
    }
}

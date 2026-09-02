<?php

namespace App\Http\Requests\Api\V1\Expense;

use App\Domain\Expense\ExpenseClaimFilterDto;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseClaimFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'nullable|string|in:pending,accepted,rejected,reimbursed',
            'per_page' => 'nullable|integer|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ExpenseClaimFilterDto
    {
        $employeeId = $this->input('employee_id');
        $user = $this->user();
        if ($user && ! $user->hasAnyPermission(['expenses.approve', 'employees.edit']) && $user->employee) {
            $employeeId = $user->employee->id;
        }

        return new ExpenseClaimFilterDto(
            employeeId: $employeeId ? (int) $employeeId : null,
            status: $this->input('status'),
            perPage: (int) $this->input('per_page', 15)
        );
    }
}

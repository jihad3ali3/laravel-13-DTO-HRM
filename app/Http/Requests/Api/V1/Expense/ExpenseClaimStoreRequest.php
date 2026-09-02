<?php

namespace App\Http\Requests\Api\V1\Expense;

use App\Domain\Expense\ExpenseClaimStoreDto;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseClaimStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:travel,meals,internet,equipment,training,other',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ExpenseClaimStoreDto
    {
        $employeeId = null;
        if ($this->filled('employee_id') && ($this->user()?->can('expenses.manage') || $this->user()?->hasRole('admin') || $this->user()?->hasRole('hr_manager'))) {
            $employeeId = (int) $this->input('employee_id');
        } elseif ($this->user()) {
            $employeeId = Employee::where('user_id', $this->user()->id)->value('id') ?? $this->user()->employee?->id;
        }

        if (! $employeeId && $this->filled('employee_id')) {
            $employeeId = (int) $this->input('employee_id');
        }

        if (! $employeeId) {
            $employeeId = (int) Employee::value('id');
        }

        $filePath = null;
        if ($this->hasFile('receipt')) {
            $filePath = $this->file('receipt')->store('receipts', 'public');
        }

        return new ExpenseClaimStoreDto(
            employeeId: (int) $employeeId,
            title: $this->input('title'),
            category: $this->input('category'),
            amount: (float) $this->input('amount'),
            expenseDate: $this->input('expense_date'),
            description: $this->input('description'),
            receiptFile: $filePath
        );
    }
}

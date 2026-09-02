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
        $employeeId = $this->input('employee_id');
        if (! $employeeId) {
            if ($this->user() && $this->user()->employee) {
                $employeeId = $this->user()->employee->id;
            } else {
                $employeeId = Employee::value('id');
            }
        }

        $filePath = null;
        if ($this->hasFile('receipt')) {
            $filePath = $this->file('receipt')->store('receipts', 'public');
        }

        return new ExpenseClaimStoreDto(
            employeeId: $employeeId,
            title: $this->input('title'),
            category: $this->input('category'),
            amount: (float) $this->input('amount'),
            expenseDate: $this->input('expense_date'),
            description: $this->input('description'),
            receiptFile: $filePath
        );
    }
}

<?php

namespace App\Http\Requests\Api\V1\Expense;

use App\Domain\Expense\ExpenseClaimStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseClaimStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:travel,meals,internet,equipment',
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
        if ($this->user() && $this->user()->employee) {
            $employeeId = $this->user()->employee->id;
        } else {
            abort(404, 'Employee record not found for logged in user.');
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

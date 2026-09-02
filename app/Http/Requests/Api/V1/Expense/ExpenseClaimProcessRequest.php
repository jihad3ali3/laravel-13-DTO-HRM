<?php

namespace App\Http\Requests\Api\V1\Expense;

use App\Domain\Expense\ExpenseClaimProcessDto;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseClaimProcessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:accepted,rejected,reimbursed',
            'reject_reason' => 'required_if:status,rejected|nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ExpenseClaimProcessDto
    {
        return new ExpenseClaimProcessDto(
            status: $this->input('status'),
            rejectReason: $this->input('reject_reason'),
            actionedBy: $this->user()?->id
        );
    }
}

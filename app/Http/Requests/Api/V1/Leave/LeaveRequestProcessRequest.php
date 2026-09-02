<?php

namespace App\Http\Requests\Api\V1\Leave;

use App\Domain\Leave\LeaveRequestProcessDto;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestProcessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:approved,rejected',
            'reject_reason' => 'required_if:status,rejected|nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): LeaveRequestProcessDto
    {
        return new LeaveRequestProcessDto(
            status: $this->input('status'),
            rejectReason: $this->input('reject_reason'),
            actionedBy: $this->user()?->id
        );
    }
}

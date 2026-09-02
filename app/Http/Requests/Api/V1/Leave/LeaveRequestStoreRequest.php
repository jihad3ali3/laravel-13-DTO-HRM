<?php

namespace App\Http\Requests\Api\V1\Leave;

use App\Domain\Leave\LeaveRequestStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'leave_id' => 'required|exists:leaves,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): LeaveRequestStoreDto
    {
        $employeeId = null;
        if ($this->user() && $this->user()->employee) {
            $employeeId = $this->user()->employee->id;
        } else {
            abort(404, 'Employee record not found for logged in user.');
        }

        return new LeaveRequestStoreDto(
            employeeId: $employeeId,
            leaveId: (int) $this->input('leave_id'),
            startDate: $this->input('start_date'),
            endDate: $this->input('end_date'),
            reason: $this->input('reason')
        );
    }
}

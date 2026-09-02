<?php

namespace App\Http\Requests\Api\V1\Leave;

use App\Domain\Leave\LeaveRequestStoreDto;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
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
        if ($this->filled('employee_id') && ($this->user()?->can('leaves.manage') || $this->user()?->hasRole('admin') || $this->user()?->hasRole('hr_manager'))) {
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

        return new LeaveRequestStoreDto(
            employeeId: (int) $employeeId,
            leaveId: (int) $this->input('leave_id'),
            startDate: $this->input('start_date'),
            endDate: $this->input('end_date'),
            reason: $this->input('reason')
        );
    }
}

<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Domain\Attendance\AttendanceCheckInDto;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceCheckInRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AttendanceCheckInDto
    {
        $authUser = auth()->user();
        if (! $authUser) {
            abort(404, 'User not found');
        }
        if (! $authUser->employee) {
            abort(404, 'Employee not found');
        }

        return AttendanceCheckInDto::fromArray([
            'employee_id' => $authUser->employee->id,
        ]);
    }
}

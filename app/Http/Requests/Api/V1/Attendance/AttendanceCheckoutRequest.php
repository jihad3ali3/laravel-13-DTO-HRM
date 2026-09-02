<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Domain\Attendance\AttendanceCheckoutDto;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceCheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [

        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AttendanceCheckoutDto
    {
        $authUser = auth()->user();
        if (! $authUser) {
            abort(404, 'User not found');
        }
        if (! $authUser->employee) {
            abort(404, 'Employee not found');
        }

        return AttendanceCheckoutDto::fromArray([
            'employee_id' => $authUser->employee->id,
        ]);
    }
}

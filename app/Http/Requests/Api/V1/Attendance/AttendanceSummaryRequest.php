<?php

namespace App\Http\Requests\Api\V1\Attendance;

use App\Domain\Attendance\AttendanceSummaryDto;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceSummaryRequest extends FormRequest
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

    public function toDto(): AttendanceSummaryDto
    {
        return AttendanceSummaryDto::fromArray($this->validated());
    }
}

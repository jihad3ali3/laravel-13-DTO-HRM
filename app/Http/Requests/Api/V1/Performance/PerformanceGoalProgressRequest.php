<?php

namespace App\Http\Requests\Api\V1\Performance;

use App\Domain\Performance\PerformanceGoalProgressDto;
use Illuminate\Foundation\Http\FormRequest;

class PerformanceGoalProgressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'progress' => 'required|integer|min:0|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): PerformanceGoalProgressDto
    {
        return new PerformanceGoalProgressDto(
            progress: (int) $this->input('progress')
        );
    }
}

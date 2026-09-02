<?php

namespace App\Http\Requests\Api\V1\Performance;

use App\Domain\Performance\PerformanceReviewStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class PerformanceReviewStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'review_cycle' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'area_of_improvements' => 'nullable|string',
            'employee_feedback' => 'nullable|string',
            'status' => 'required|string|in:draft,submitted,acknowledge',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): PerformanceReviewStoreDto
    {
        return new PerformanceReviewStoreDto(
            employeeId: (int) $this->input('employee_id'),
            reviewerId: (int) $this->user()?->id,
            reviewCycle: $this->input('review_cycle'),
            rating: (int) $this->input('rating'),
            strengths: $this->input('strengths'),
            areaOfImprovements: $this->input('area_of_improvements'),
            employeeFeedback: $this->input('employee_feedback'),
            status: $this->input('status', 'draft')
        );
    }
}

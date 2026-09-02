<?php

namespace App\Http\Requests\Api\V1\Payroll;

use App\Domain\Payroll\PayrollGenerateDto;
use Illuminate\Foundation\Http\FormRequest;

class PayrollGenerateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): PayrollGenerateDto
    {
        return new PayrollGenerateDto(
            month: (int) $this->input('month'),
            year: (int) $this->input('year')
        );
    }
}

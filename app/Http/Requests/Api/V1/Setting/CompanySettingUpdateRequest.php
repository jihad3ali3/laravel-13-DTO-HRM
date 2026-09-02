<?php

namespace App\Http\Requests\Api\V1\Setting;

use App\Domain\Setting\CompanySettingUpdateDto;
use Illuminate\Foundation\Http\FormRequest;

class CompanySettingUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('settings')) {
            $this->merge([
                'settings' => $this->except(['_token', '_method']),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'settings' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): CompanySettingUpdateDto
    {
        return new CompanySettingUpdateDto(
            settings: $this->input('settings')
        );
    }
}

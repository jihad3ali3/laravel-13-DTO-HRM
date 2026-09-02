<?php

namespace App\Http\Requests\Api\V1\Holiday;

use App\Domain\Holiday\HolidayStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class HolidayStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'holiday_date' => 'required|date',
            'type' => 'required|string|in:public,company,optional',
            'description' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): HolidayStoreDto
    {
        return new HolidayStoreDto(
            name: $this->input('name'),
            holidayDate: $this->input('holiday_date'),
            type: $this->input('type', 'public'),
            description: $this->input('description')
        );
    }
}

<?php

namespace App\Http\Requests\Api\V1\Setting;

use App\Domain\Setting\DepartmentStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:departments,name,'.$this->route('id'),
            'code' => 'required|string|unique:departments,code,'.$this->route('id'),
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): DepartmentStoreDto
    {
        return new DepartmentStoreDto(
            name: $this->input('name'),
            code: $this->input('code'),
            description: $this->input('description'),
            isActive: $this->boolean('is_active', true)
        );
    }
}

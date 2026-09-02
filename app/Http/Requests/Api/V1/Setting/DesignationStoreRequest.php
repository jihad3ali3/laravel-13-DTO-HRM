<?php

namespace App\Http\Requests\Api\V1\Setting;

use App\Domain\Setting\DesignationStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class DesignationStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): DesignationStoreDto
    {
        return new DesignationStoreDto(
            departmentId: (int) $this->input('department_id'),
            name: $this->input('name'),
            description: $this->input('description'),
            isActive: $this->boolean('is_active', true)
        );
    }
}

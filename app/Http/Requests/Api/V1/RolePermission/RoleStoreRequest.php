<?php

namespace App\Http\Requests\Api\V1\RolePermission;

use App\Domain\RolePermission\RoleStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name,'.$this->route('id'),
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): RoleStoreDto
    {
        return new RoleStoreDto(
            name: $this->input('name'),
            permissions: $this->input('permissions', [])
        );
    }
}

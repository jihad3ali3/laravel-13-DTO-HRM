<?php

namespace App\Http\Requests\Api\V1\RolePermission;

use App\Domain\RolePermission\RoleAssignDto;
use Illuminate\Foundation\Http\FormRequest;

class RoleAssignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string|exists:roles,name',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): RoleAssignDto
    {
        return new RoleAssignDto(
            userId: (int) $this->input('user_id'),
            roleName: $this->input('role_name')
        );
    }
}

<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Domain\Auth\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'remember_me' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): LoginDTO
    {
        return LoginDTO::fromArray($this->validated());
    }
}

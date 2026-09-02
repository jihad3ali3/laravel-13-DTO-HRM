<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}
    public function login(LoginRequest $request)
    {
        $this->authService->login($request->toDto());
        return redirect()->route('admin::dashboard.admin');
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->toDto());

        return ApiResponse::success($result, 'Login success');
    }

    public function logout(Request $logoutRequest)
    {
        $this->authService->logout($logoutRequest->user());

        return ApiResponse::success(null, 'Logout success');
    }

    public function profile(Request $profileRequest)
    {
        return ApiResponse::success($profileRequest->user());
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginDriverRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function loginDriver(LoginDriverRequest $request)
    {
        $result = $this->authService->loginDriver($request);
        return response()->json($result, $result['status']);
    }

    public function refresh(Request $request)
    {
        $token = $request->input('refresh_token');
        $newTokens = $this->authService->refreshToken($token);

        if (!$newTokens) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        return response()->json($newTokens);
    }
}

<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\LoginDriverRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private SanctumTokenService $tokenService;
    private LoginAttemptService $loginAttemptService;

    public function __construct(
        SanctumTokenService $tokenService,
        LoginAttemptService $loginAttemptService
    ) {
        $this->tokenService = $tokenService;
        $this->loginAttemptService = $loginAttemptService;
    }

    public function loginDriver(LoginDriverRequest $request): array
    {
        $data = $request->validated();
        $phone = $data['phone'];

        if ($this->loginAttemptService->tooManyAttempts($request)) {
            return [
                "success" => false,
                "error_code" => "RATE_LIMITED",
                "message" => "Bạn thao tác quá nhanh. Vui lòng thử lại sau.",
                "status" => 429
            ];
        }

        $driver = User::where('phone', $phone)->first();

        if (!$driver) {
            $this->loginAttemptService->hit($request);
            return [
                "success" => false,
                "error_code" => "INVALID_ACCOUNT",
                "message" => "Biển số xe hoặc số điện thoại không hợp lệ",
                "status" => 401
            ];
        }

        if ($this->loginAttemptService->isLocked($driver)) {
            return [
                "success" => false,
                "error_code" => "ACCOUNT_LOCKED",
                "message" => "Tài khoản bị khoá tạm thời do nhập sai quá số lần cho phép. Vui lòng thử lại sau.",
                "status" => 423
            ];
        }

        if (!Hash::check($data['password'], $driver->password)) {
            $this->loginAttemptService->hit($request);
            $this->loginAttemptService->incrementFailedAttempts($driver);
            return [
                "success" => false,
                "error_code" => "INVALID_CREDENTIALS",
                "message" => "Mật khẩu không đúng",
                "status" => 401
            ];
        }

        if ($driver->status !== 1) {
            return [
                "success" => false,
                "error_code" => "ACCOUNT_INACTIVE",
                "message" => "Tài khoản đang không hoạt động. Vui lòng liên hệ hỗ trợ.",
                "status" => 403
            ];
        }

        // if (!$driver->isDriver()) {
        //     return [
        //         "success" => false,
        //         "error_code" => "NOT_A_DRIVER",
        //         "message" => "Tài khoản này không phải tài xế.",
        //         "status" => 403
        //     ];
        // }

        $this->loginAttemptService->clear($request);
        $this->loginAttemptService->clearFailedAttempts($driver);

        $tokens = $this->tokenService->createToken($driver);

        return [
            "success" => true,
            "data" => array_merge([
                "driver" => [
                    "id"            => $driver->id,
                    "name"          => $driver->full_name,
                    "phone_number"  => $driver->phone,
                    "license_plate" => $driver->license_plate,
                    "status"        => $driver->status,
                ]
            ], $tokens),
            "status" => 200
        ];
    }

    public function refreshToken(string $refreshToken): ?array
    {
        return $this->tokenService->refreshToken($refreshToken);
    }
}

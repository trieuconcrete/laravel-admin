<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenService implements TokenServiceInterface
{
    public function createToken(User $user): array
    {
        $expiresAt = Carbon::now()->addHours(2);
        $refreshExpiresAt = Carbon::now()->addDays(7);

        $accessToken = $user->createToken(
            'api_token',
            ['*'],
            $expiresAt
        )->plainTextToken;

        $refreshToken = Str::random(64);
        RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => hash('sha256', $refreshToken),
            'expires_at' => $refreshExpiresAt,
        ]);

        return [
            'access_token'  => $accessToken,
            'expires_in'    => (int) Carbon::now()->diffInSeconds($expiresAt),
            'token_type'    => 'Bearer',
            'refresh_token' => $refreshToken,
            'refresh_expires_in'    => (int) Carbon::now()->diffInSeconds($refreshExpiresAt),
        ];
    }

    public function validateToken(string $token): ?User
    {
        $model = PersonalAccessToken::findToken($token);

        if (! $model) {
            return null;
        }

        if ($model->expires_at && $model->expires_at->isPast()) {
            return null;
        }

        return $model->tokenable;
    }

    public function revokeToken(string $token): bool
    {
        $model = PersonalAccessToken::findToken($token);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }

    public function refreshToken(string $refreshToken): ?array
    {
        $hashed = hash('sha256', $refreshToken);

        $record = RefreshToken::where('token', $hashed)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $record) {
            return null;
        }

        $user = $record->user;

        $user->tokens()->delete();

        $accessToken = $user->createToken('api_token')->plainTextToken;

        return [
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => (int) Carbon::now()->diffInSeconds(Carbon::now()->addHours(2)),
        ];
    }
}

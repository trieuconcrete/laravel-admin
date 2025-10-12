<?php

namespace App\Services\Auth;

use App\Models\User;

interface TokenServiceInterface
{
    public function createToken(User $user): array;
    public function validateToken(string $token): ?User;
    public function revokeToken(string $token): bool;
     public function refreshToken(string $refreshToken): ?array;
}

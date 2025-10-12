<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LoginAttemptService
{
    private int $maxAttempts = 5;
    private int $decaySeconds = 60;
    private int $lockMinutes = 15;

    public function tooManyAttempts(Request $request): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->key($request),
            $this->maxAttempts
        );
    }

    public function hit(Request $request): void
    {
        RateLimiter::hit($this->key($request), $this->decaySeconds);
    }

    public function clear(Request $request): void
    {
        RateLimiter::clear($this->key($request));
    }

    public function incrementFailedAttempts(User $user): void
    {
        $user->failed_attempts++;

        if ($user->failed_attempts >= $this->maxAttempts) {
            $user->is_locked = true;
            $user->locked_until = now()->addMinutes($this->lockMinutes);
        }

        $user->save();
    }

    public function clearFailedAttempts(User $user): void
    {
        $user->failed_attempts = 0;
        $user->is_locked = false;
        $user->locked_until = null;
        $user->save();
    }

    public function isLocked(User $user): bool
    {
        if (!$user->is_locked) {
            return false;
        }

        if ($user->locked_until && now()->greaterThan($user->locked_until)) {
            $this->clearFailedAttempts($user);
            return false;
        }

        return true;
    }

    private function key(Request $request): string
    {
        return 'login-driver:' . $request->ip();
    }
}

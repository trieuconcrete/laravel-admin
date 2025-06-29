<?php

namespace App\Repositories;

use App\Models\SalaryAdvanceRequest;
use App\Models\User;
use App\Repositories\Interface\SalaryAdvanceRequestRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SalaryAdvanceRequestRepository implements SalaryAdvanceRequestRepositoryInterface
{
    /**
     * Create a new salary advance request
     *
     * @param array $data
     * @return SalaryAdvanceRequest
     */
    public function create(array $data): SalaryAdvanceRequest
    {
        return SalaryAdvanceRequest::create($data);
    }

    /**
     * Get salary advance requests by user
     *
     * @param User $user
     * @return Collection
     */
    public function getByUser(User $user): Collection
    {
        return SalaryAdvanceRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get salary advance requests by user and month
     *
     * @param mixed $user User object or user ID
     * @param string $month Format: Y-m
     * @return Collection
     */
    public function getByUserAndMonth($user, string $month): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        return SalaryAdvanceRequest::where('user_id', $userId)
            ->whereBetween('advance_month', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

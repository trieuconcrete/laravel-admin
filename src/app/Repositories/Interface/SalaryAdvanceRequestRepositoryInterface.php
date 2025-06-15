<?php

namespace App\Repositories\Interface;

use App\Models\SalaryAdvanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface SalaryAdvanceRequestRepositoryInterface
{
    /**
     * Create a new salary advance request
     *
     * @param array $data
     * @return SalaryAdvanceRequest
     */
    public function create(array $data): SalaryAdvanceRequest;

    /**
     * Get salary advance requests by user
     *
     * @param User $user
     * @return Collection
     */
    public function getByUser(User $user): Collection;

    /**
     * Get salary advance requests by user and month
     *
     * @param User $user
     * @param string $month Format: Y-m
     * @return Collection
     */
    public function getByUserAndMonth(User $user, string $month): Collection;
}

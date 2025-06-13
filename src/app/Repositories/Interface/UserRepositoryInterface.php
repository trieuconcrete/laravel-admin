<?php

namespace App\Repositories\Interface;

use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getUsers();
    public function activeUsers();
    public function inactiveUsers();
    public function usersCreatedThisMonth();
    public function usersCreatedToday();

    /**
     * Summary of getUserByConditions
     * @param array $conditions
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserByConditions(array $conditions = []);

    /**
     * Summary of getUsersWithFilters
     * @param array $filters
     * @param mixed $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersWithFilters(array $filters = [], ?int $perPage = 10);
}

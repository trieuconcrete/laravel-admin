<?php

namespace App\Repositories\Interface;

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
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function getUserByConditions(array $conditions = []);
}

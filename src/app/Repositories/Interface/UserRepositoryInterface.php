<?php

namespace App\Repositories\Interface;

interface UserRepositoryInterface
{
    public function getUsers();
    public function activeUsers();
    public function inactiveUsers();
    public function usersCreatedThisMonth();
    public function usersCreatedToday();
}

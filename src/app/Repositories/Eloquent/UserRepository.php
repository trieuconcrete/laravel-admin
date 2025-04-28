<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUsers()
    {
        return $this->model->select('id', 'status', 'created_at')->get();
    }

    public function activeUsers()
    {
        return $this->model->where('status', 1)->get();
    }

    public function inactiveUsers()
    {
        return $this->model->where('status', 0)->get();
    }

    public function usersCreatedThisMonth()
    {
        return $this->model->whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->get();
    }

    public function usersCreatedToday()
    {
        return $this->model->whereDate('created_at', today())->get();
    }
}

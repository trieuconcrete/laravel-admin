<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\Shipment;
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

    /**
     * Summary of getUserByConditions
     * @param array $conditions
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserByConditions(array $conditions = [])
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * Summary of getUsersWithFilters
     * @param array $filters
     * @param mixed $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersWithFilters(array $filters = [], ?int $perPage = 10) 
    {
        $query = $this->model->query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('employee_code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply position filter
        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Exclude current user and deleted users
        if (!empty($filters['exclude_current_user'])) {
            $query->where('id', '!=', $filters['exclude_current_user']);
        }
        
        $query->whereNull('deleted_at');
        
        return $query->latest()->paginate($perPage);
    }
}

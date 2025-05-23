<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Interface\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    /**
     * Summary of __construct
     * @param \App\Models\Customer $model
     */
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    /**
     * Summary of getCustomerWithFilters
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getCustomerWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with([])->latest();

        /** search vehicle type */
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        /** search status */
        if (!empty($filters['is_active'])) {
            $isActive = $filters['is_active'] == Customer::STATUS_ACTIVE ? true : false;
            $query->where('is_active', $isActive);
        }

        /** search keyword */
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('manufacemailtured_year', 'like', '%' . $keyword . '%');
            });
        }

        return $query->paginate(10);
    }    
}

<?php

namespace App\Repositories\Interface;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Customer;

/**
 * Summary of CustomerRepositoryInterface
 */
interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getCustomerWithFilters
     * @param array $filters
     * @return void
     */
    public function getCustomerWithFilters(array $filters): LengthAwarePaginator;
}

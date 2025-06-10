<?php

namespace App\Repositories\Interface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use App\Models\Customer;

/**
 * Summary of CustomerRepositoryInterface
 */
interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getCustomerWithFilters
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getCustomerWithFilters(array $filters): LengthAwarePaginator;
    
    /**
     * Get monthly shipments for a customer
     * @param int $customerId
     * @param string $month Format: 'YYYY-MM'
     * @return Collection
     */
    public function getMonthlyShipments(int $customerId, string $month);
}

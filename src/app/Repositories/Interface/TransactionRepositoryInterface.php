<?php

namespace App\Repositories\Interface;

use App\Models\Customer;
use App\Models\Transaction;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new transaction for a customer
     *
     * @param array $data
     * @return Transaction
     */
    public function createCustomerTransaction(array $data): Transaction;
    
    /**
     * Get customer transactions with filters
     *
     * @param Customer $customer
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCustomerTransactions(Customer $customer, array $filters = [], int $perPage = 10);
}

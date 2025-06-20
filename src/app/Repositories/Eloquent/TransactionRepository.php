<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Payment;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * TransactionRepository constructor.
     *
     * @param Transaction $model
     */
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new transaction for a customer
     *
     * @param array $data
     * @return Transaction
     */
    public function createCustomerTransaction(array $data): Transaction
    {
        return $this->model->create($data);
    }
    
    /**
     * Get customer transactions with filters
     *
     * @param Customer $customer
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCustomerTransactions(Customer $customer, array $filters = [], int $perPage = 10)
    {
        $query = $this->model
            ->select(
                'transactions.*',
                'payments.payment_date',
                'payments.method',
                'payments.status',
                'payments.notes',
                'users.full_name as created_by_name'
            )
            ->join('payments', 'transactions.payment_id', '=', 'payments.id')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->where('payments.customer_id', $customer->id);
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('payments.payment_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('payments.payment_date', '<=', $filters['end_date']);
        }
        
        if (!empty($filters['amount_min'])) {
            $query->where('transactions.amount', '>=', $filters['amount_min']);
        }
        
        if (!empty($filters['amount_max'])) {
            $query->where('transactions.amount', '<=', $filters['amount_max']);
        }
        
        if (!empty($filters['transaction_type'])) {
            $query->where('transactions.type', $filters['transaction_type']);
        }
        
        if (!empty($filters['payment_method'])) {
            $query->where('payments.method', $filters['payment_method']);
        }
        
        if (!empty($filters['payment_status'])) {
            $query->where('payments.status', $filters['payment_status']);
        }
        
        // Order by payment date (latest first)
        $query->orderBy('payments.payment_date', 'desc');
        
        return $query->paginate($perPage);
    }
}

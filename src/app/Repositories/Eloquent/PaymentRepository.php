<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Interface\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    /**
     * PaymentRepository constructor.
     *
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new payment for a customer
     *
     * @param array $data
     * @return Payment
     */
    public function createCustomerPayment(array $data): Payment
    {
        return $this->model->create($data);
    }
}

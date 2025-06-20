<?php

namespace App\Repositories\Interface;

use App\Models\Payment;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new payment for a customer
     *
     * @param array $data
     * @return Payment
     */
    public function createCustomerPayment(array $data): Payment;
}

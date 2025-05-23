<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CustomerService
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerService constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Summary of getFilteredCustomer
     * @param array $filters
     */
    public function getFilteredCustomer(array $filters)
    {
        return $this->customerRepository->getCustomerWithFilters($filters);
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['is_active'] = $data['is_active'] == Customer::STATUS_ACTIVE ? true : false;

        return $this->customerRepository->create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $user
     * @return void
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->all();

        return $this->customerRepository->update($customer->id, $data);
    }
}

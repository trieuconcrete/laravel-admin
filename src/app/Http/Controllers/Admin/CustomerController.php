<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Services\CustomerService;
use App\Http\Requests\Customer\StoreCustomerRequest;


class CustomerController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\CustomerService $customerService
     * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected CustomerService $customerService,
        protected CustomerRepository $customerRepository
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'is_active', 'keyword']);

        $customers = $this->customerService->getFilteredCustomer($filters);
        $customerTypes = Customer::getTypes();
        $customerStatusActives = Customer::getStatusActives();

        return view('admin.customers.index', compact('customers', 'customerTypes', 'customerStatusActives'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->customerService->store($request);

            DB::commit();

            return response()->json(['message' => 'Customer created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function show(Customer $customer)
    {
        $customer = Customer::first();
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer = Customer::first();
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        try {
            return redirect()->route('admin.customers.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return back()->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}

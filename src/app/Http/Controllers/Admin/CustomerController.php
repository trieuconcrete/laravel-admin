<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\TransactionPaymentService;
use App\Exports\InvoiceExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Models\Transaction;
use App\Models\Payment;

/**
 * Summary of __construct
 * @param \App\Services\CustomerService $customerService
 * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
 */
class CustomerController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\CustomerService $customerService
     * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected CustomerService $customerService,
        protected CustomerRepository $customerRepository,
        protected TransactionPaymentService $transactionPaymentService
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

    /**
     * Summary of show
     * @param \App\Models\Customer $customer
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Customer $customer, Request $request)
    {
        // Handle AJAX request for monthly shipments by month
        if ($request->ajax() && $request->has('month')) {
            try {
                $month = $request->input('month');
                $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $month);
                
                return response()->json([
                    'success' => true,
                    'data' => $monthlyShipments
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching monthly report: ' . $e->getMessage()
                ], 500);
            }
        }
        
        // Get current month in YYYY-MM format for initial data
        $currentMonth = date('Y-m');
        $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $currentMonth);
        $typeTransactions = Transaction::getTypes();
        $paymentMethods = Payment::getPaymentMethods();
        $paymentStatuses = Payment::getStatuses();
        
        // Load all transactions by default
        try {
            $perPage = 10;
            $transactions = $this->transactionPaymentService->getCustomerTransactions($customer, [], $perPage);
            $activeTab = $request->input('active_tab', 'transactions');
            $filters = [];
            
            return view('admin.customers.show', compact(
                'customer', 
                'monthlyShipments', 
                'typeTransactions', 
                'transactions', 
                'activeTab',
                'paymentMethods',
                'paymentStatuses',
                'filters'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading default transactions', ['error' => $e->getMessage(), 'customer_id' => $customer->id]);
            return view('admin.customers.show', compact('customer', 'monthlyShipments', 'typeTransactions'));
        }
    }

    public function edit(Customer $customer)
    {
        $customer = Customer::first();
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Customer $customer)
    {
        DB::beginTransaction();
        try {
            $this->customerService->update($request, $customer);

            DB::commit();
            return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
    
    /**
     * Export invoice for customer
     *
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportInvoice(Customer $customer, Request $request)
    {
        try {
            $month = $request->input('month', date('Y-m'));
            $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $month);
            $taxRate = $request->input('tax_rate', 8.0); // Default tax rate is 8%
            
            // Format month name for filename
            $monthDate = \DateTime::createFromFormat('Y-m', $month);
            $monthName = $monthDate->format('m-Y');
            
            $filename = "hoa_don_{$customer->name}_{$monthName}.xlsx";
            
            return Excel::download(
                new InvoiceExport($customer, $monthlyShipments, $month, $taxRate),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}

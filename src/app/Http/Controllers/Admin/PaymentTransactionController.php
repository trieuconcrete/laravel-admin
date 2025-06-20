<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\SearchTransactionRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\TransactionPaymentService;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Models\Transaction;
use App\Models\Payment;

/**
 * Summary of __construct
 * @param \App\Services\CustomerService $customerService
 * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
 */
class PaymentTransactionController extends Controller
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
    
    /**
     * Store a new transaction for a customer
     *
     * @param Customer $customer
     * @param StoreTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTransaction(Customer $customer, StoreTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Format amount if it comes as a formatted string
            if (is_string($data['amount'])) {
                $data['amount'] = (float) str_replace([',', '.'], ['', '.'], $data['amount']);
            }
            
            $result = $this->transactionPaymentService->createCustomerTransaction($customer, $data);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'transaction' => $result['transaction'],
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get transactions for a customer with search filters
     *
     * @param Customer $customer
     * @param SearchTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function getTransactions(Customer $customer, SearchTransactionRequest $request)
    {
        try {
            $filters = $request->validated();
            $activeTab = $request->input('active_tab', 'transactions');
            $perPage = $request->input('per_page', 10);
            
            // Format numeric filters if they come as formatted strings
            if (!empty($filters['amount_min']) && is_string($filters['amount_min'])) {
                $filters['amount_min'] = (float) str_replace([',', '.'], ['', '.'], $filters['amount_min']);
            }
            
            if (!empty($filters['amount_max']) && is_string($filters['amount_max'])) {
                $filters['amount_max'] = (float) str_replace([',', '.'], ['', '.'], $filters['amount_max']);
            }
            
            $transactions = $this->transactionPaymentService->getCustomerTransactions($customer, $filters, $perPage);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.customers.partials.transaction-table', compact('transactions', 'customer'))->render(),
                    'pagination' => view('admin.partials.pagination', ['paginator' => $transactions])->render()
                ]);
            }
            
            // For regular requests, return to the show view with the active tab
            $currentMonth = date('Y-m');
            $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $currentMonth);
            $typeTransactions = Transaction::getTypes();
            $paymentMethods = Payment::getPaymentMethods();
            $paymentStatuses = Payment::getStatuses();
            
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
            Log::error('Error retrieving transactions', ['error' => $e->getMessage(), 'customer_id' => $customer->id]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Update a transaction
     *
     * @param Customer $customer
     * @param Transaction $transaction
     * @param StoreTransactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransaction(Customer $customer, Transaction $transaction, StoreTransactionRequest $request)
    {
        try {
            // Check if transaction belongs to customer
            if ($transaction->payment && $transaction->payment->customer_id != $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không thuộc về khách hàng này.'
                ], 403);
            }
            
            $data = $request->validated();
            
            // Format amount if it comes as a formatted string
            if (is_string($data['amount'])) {
                $data['amount'] = (float) str_replace([',', '.'], ['', '.'], $data['amount']);
            }
            
            // Update transaction using service
            $result = $this->transactionPaymentService->updateCustomerTransaction($transaction, $data);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'transaction' => $result['transaction'],
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Transaction update failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a transaction
     *
     * @param Customer $customer
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyTransaction(Customer $customer, Transaction $transaction)
    {
        try {
            // Check if transaction belongs to customer
            if ($transaction->payment && $transaction->payment->customer_id != $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch không thuộc về khách hàng này.'
                ], 403);
            }
            
            // Delete transaction using service
            $result = $this->transactionPaymentService->deleteCustomerTransaction($transaction);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Transaction deletion failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

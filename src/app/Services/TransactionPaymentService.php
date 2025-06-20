<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Transaction;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\PaymentRepositoryInterface;
use App\Repositories\Interface\TransactionRepositoryInterface;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Transaction\SearchTransactionRequest;

class TransactionPaymentService
{
    /**
     * @var PaymentRepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * TransactionPaymentService constructor.
     *
     * @param PaymentRepositoryInterface $paymentRepository
     * @param TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Create a new transaction for a customer
     *
     * @param Customer $customer
     * @param array $data
     * @return array
     */
    public function createCustomerTransaction(Customer $customer, array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Create payment record
            $paymentData = [
                'customer_id' => $customer->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'status' => Payment::STATUS_COMPLETED,
                'notes' => $data['notes'] ?? null,
                'method' => $data['payment_method'],
                'created_by' => Auth::id(),
            ];
            
            $payment = $this->paymentRepository->createCustomerPayment($paymentData);
            
            // Create transaction record
            $transactionData = [
                'payment_id' => $payment->id,
                'type' => Transaction::TYPE_INCOME,
                'amount' => $data['amount'],
                'category' => Transaction::CATEGORY_CUSTOMER_PAYMENT,
                'description' => $data['notes'] ?? 'Khách hàng thanh toán',
                'transaction_date' => $data['payment_date'],
                'created_by' => Auth::id(),
            ];
            
            $transaction = $this->transactionRepository->createCustomerTransaction($transactionData);
            
            DB::commit();
            
            return [
                'success' => true,
                'payment' => $payment,
                'transaction' => $transaction,
                'message' => 'Giao dịch đã được tạo thành công.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ];
        }
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
        try {
            return $this->transactionRepository->getCustomerTransactions($customer, $filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error retrieving customer transactions', ['error' => $e->getMessage(), 'customer_id' => $customer->id]);
            throw $e;
        }
    }
    
    /**
     * Update an existing transaction
     *
     * @param Transaction $transaction
     * @param array $data
     * @return array
     */
    public function updateCustomerTransaction(Transaction $transaction, array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Update payment record if it exists
            if ($transaction->payment) {
                $paymentData = [
                    'amount' => $data['amount'],
                    'payment_date' => $data['payment_date'],
                    'notes' => $data['notes'] ?? null,
                    'method' => $data['payment_method'],
                    'updated_by' => Auth::id(),
                ];
                
                $payment = $this->paymentRepository->update($transaction->payment->id, $paymentData);
                
                // Update transaction record
                $transactionData = [
                    'amount' => $data['amount'],
                    'description' => $data['notes'] ?? 'Khách hàng thanh toán',
                    'transaction_date' => $data['payment_date'],
                    'updated_by' => Auth::id(),
                ];
                
                $transaction = $this->transactionRepository->update($transaction->id, $transactionData);
                
                DB::commit();
                
                return [
                    'success' => true,
                    'payment' => $payment,
                    'transaction' => $transaction,
                    'message' => 'Giao dịch đã được cập nhật thành công.'
                ];
            } else {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin thanh toán cho giao dịch này.'
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction update failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete a transaction and its associated payment
     *
     * @param Transaction $transaction
     * @return array
     */
    public function deleteCustomerTransaction(Transaction $transaction): array
    {
        DB::beginTransaction();
        
        try {
            // Store payment ID for later deletion
            $paymentId = $transaction->payment_id;
            
            // Delete transaction first
            $this->transactionRepository->delete($transaction->id);
            
            // Delete associated payment if it exists
            if ($paymentId) {
                $this->paymentRepository->delete($paymentId);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Giao dịch đã được xóa thành công.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction deletion failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ];
        }
    }
}

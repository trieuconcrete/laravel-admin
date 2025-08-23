<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\CarRental;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\InvoiceExport;
use App\Models\ShipmentReport;
use App\Services\CustomerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\TransactionPaymentService;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;

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
        // Handle AJAX request for customer data (for auto-fill forms)
        if ($request->ajax() && $request->has('get_customer_data')) {
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'type' => $customer->type,
                'is_active' => $customer->is_active
            ]);
        }

        // Handle AJAX request for monthly shipments by month
        if ($request->ajax() && $request->has('month')) {
            try {
                $month = $request->input('month');
                $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $month);
                
                // Check if report is finalized for this month
                $shipmentReport = \App\Models\ShipmentReport::where('customer_id', $customer->id)
                    ->where('monthly', $month)
                    ->first();
                
                $isFinalized = $shipmentReport ? $shipmentReport->is_finalized : false;
                
                return response()->json([
                    'success' => true,
                    'data' => $monthlyShipments,
                    'isFinalized' => $isFinalized
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
        $customerStatusActives = Customer::getStatusActives();
        
        // Lấy danh sách các tháng có báo cáo (unique months)
        $shipmentMonthlyReports = \App\Models\ShipmentReport::where('customer_id', $customer->id)
            ->whereNull('car_rental_id')
            ->select('monthly')
            ->distinct()
            ->orderBy('monthly', 'desc')
            ->get();

        // Load all transactions by default
        try {
            $perPage = 10;
            $transactions = $this->transactionPaymentService->getCustomerTransactions($customer, [], $perPage);
            $activeTab = $request->input('active_tab', 'monthlyReport');
            $filters = [];
            $query = CarRental::with(['customer', 'shipmentReports'])->where('customer_id', $customer->id)->orderBy('created_at', 'DESC');
            /** search vehicle type */
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            /** search status */
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            $carRentals = $query->paginate($perPage);
            
            return view('admin.customers.show', compact(
                'customer', 
                'monthlyShipments', 
                'typeTransactions', 
                'transactions', 
                'activeTab',
                'paymentMethods',
                'paymentStatuses',
                'filters',
                'customerStatusActives',
                'shipmentMonthlyReports',
                'carRentals'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading default transactions', ['error' => $e->getMessage(), 'customer_id' => $customer->id]);
            return view('admin.customers.show', compact('customer', 'monthlyShipments', 'typeTransactions'));
        }
    }

    /**
     * Summary of edit
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Customer $customer)
    {
        return redirect()->route('admin.customers.index')->with('error', 'Trang không hợp lệ.');
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

    /**
     * Tổng kết bảng kê theo tháng
     *
     * @param Customer $customer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summarizeMonthlyReport(Customer $customer, Request $request)
    {
        try {
            $month = $request->input('month', date('Y-m'));
            $userId = Auth::id();
            
            // Lấy dữ liệu shipments theo tháng
            $monthlyShipments = $this->customerService->getMonthlyShipments($customer->id, $month);
            
            if ($monthlyShipments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu chuyến hàng trong tháng này để tổng kết'
                ], 400);
            }
            
            // Tính tổng số tiền chỉ từ shipment đã hoàn thành
            $totalAmount = $monthlyShipments->sum('total_amount');
            
            // Tạo hoặc cập nhật báo cáo
            $report = ShipmentReport::createOrUpdateMonthlyReport(
                $customer->id,
                $month,
                $totalAmount,
                $userId
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Tổng kết bảng kê thành công',
                'data' => [
                    'month' => $month,
                    'total_amount' => $totalAmount,
                    'formatted_amount' => number_format($totalAmount, 0, ',', '.'),
                    'shipment_count' => $monthlyShipments->count(),
                    'report_id' => $report->id,
                    'created_at' => $report->created_at->format('d/m/Y H:i'),
                    'updated_at' => $report->updated_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error summarizing monthly report', [
                'customer_id' => $customer->id,
                'month' => $request->input('month'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tổng kết bảng kê: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tổng kết công nợ khách hàng từ trước đến nay
     *
     * @param Customer $customer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDebtSummary(Customer $customer, Request $request)
    {
        try {
            // Luôn lấy debt summary tổng cộng từ trước đến nay
            $debtSummary = ShipmentReport::getCustomerDebtSummary($customer->id);

            return response()->json([
                'success' => true,
                'debt_summary' => $debtSummary
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting debt summary', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy tổng kết công nợ: ' . $e->getMessage()
            ], 500);
        }
    }
}

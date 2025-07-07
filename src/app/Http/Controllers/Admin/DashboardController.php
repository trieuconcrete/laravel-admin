<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interface\UserRepositoryInterface;
use App\Models\Shipment;
use App\Models\ShipmentDeduction;
use App\Models\ShipmentDeductionType;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\ShipmentReport;

class DashboardController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $now = Carbon::now();

        // Count vehicles
        $totalVehicles = Vehicle::count();

        // Count drivers
        $totalDrivers = User::where('role', User::ROLE_DRIVER)->count();

        // Count customers
        $totalCustomers = Customer::count();

        // Calculate total debt across all customers
        $totalDebt = $this->calculateTotalDebt();

        // Calculate total salary for the current month
        $totalSalaryThisMonth = User::sum('salary_base');

        // Keep track of users for backward compatibility
        $users = $this->userRepository->getUsers();
        $usersToday = $users->filter(function ($user) use ($now) {
            return $user->created_at->isToday();
        })->count();
        
        // Get data for the last 6 months for charts
        $lastSixMonths = $this->getLastSixMonths();
        
        // Get shipment counts by month
        $shipmentCounts = $this->getShipmentCountsByMonth($lastSixMonths);
        
        // Get income vs expenses data
        $financialData = $this->getIncomeVsExpensesByMonth($lastSixMonths);
        
        // Get debt data by month
        $debtData = $this->getDebtByMonth($lastSixMonths);
        
        // Format chart data for JavaScript
        $chartData = [
            'months' => $lastSixMonths->map(function($date) {
                return $date->format('m/Y');
            })->toArray(),
            'shipmentCounts' => array_values($shipmentCounts),
            'income' => array_values($financialData['income']),
            'expenses' => array_values($financialData['expenses']),
            'debt' => array_values($debtData)
        ];

        return view('admin.dashboard', compact(
            'totalVehicles',
            'totalDrivers',
            'totalCustomers',
            'totalDebt',
            'totalSalaryThisMonth',
            'usersToday',
            'chartData'
        ));
    }
    
    /**
     * Get the last 6 months as Carbon instances
     *
     * @return \Illuminate\Support\Collection
     */
    private function getLastSixMonths()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->startOfMonth()->subMonths($i)->startOfMonth());
        }
        return $months;
    }
    
    /**
     * Get shipment counts for each month
     *
     * @param \Illuminate\Support\Collection $months
     * @return array
     */
    private function getShipmentCountsByMonth($months)
    {
        $shipmentCounts = [];
        
        foreach ($months as $month) {
            $startDate = $month->copy()->startOfMonth()->format('Y-m-d H:i:s');
            $endDate = $month->copy()->endOfMonth()->format('Y-m-d H:i:s');
            
            $count = Shipment::whereBetween('created_at', [$startDate, $endDate])->count();
            $shipmentCounts[$month->format('m/Y')] = $count;
        }
        
        return $shipmentCounts;
    }
    
    /**
     * Get income vs expenses data for each month
     *
     * @param \Illuminate\Support\Collection $months
     * @return array
     */
    private function getIncomeVsExpensesByMonth($months)
    {
        $income = [];
        $expenses = [];
        
        foreach ($months as $month) {
            $startDate = $month->copy()->startOfMonth()->format('Y-m-d H:i:s');
            $endDate = $month->copy()->endOfMonth()->format('Y-m-d H:i:s');

            // Tính tổng thu nhập (income)
            $monthlyIncome = Transaction::where('type', Transaction::TYPE_INCOME)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            
            // Tính tổng chi phí (expense)
            $monthlyExpenses = Transaction::where('type', Transaction::TYPE_EXPENSE)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            
            $income[$month->format('m/Y')] = $monthlyIncome;
            $expenses[$month->format('m/Y')] = $monthlyExpenses;
        }

        return [
            'income' => $income,
            'expenses' => $expenses
        ];
    }
    
    /**
     * Calculate total debt across all customers
     *
     * @return float
     */
    private function calculateTotalDebt()
    {
        // Method 1: Use shipment reports (tổng kết)
        $totalReported = ShipmentReport::sum('total_amount');
        
        // Method 2: Calculate directly from shipments if no reports available
        if ($totalReported == 0) {
            $totalReported = DB::table('shipments')
                ->selectRaw('SUM(
                    (COALESCE(trip_count, 1) * COALESCE(unit_price, 0)) - 
                    (SELECT COALESCE(SUM(amount), 0) FROM shipment_deductions WHERE shipment_id = shipments.id)
                ) as total_value')
                ->value('total_value') ?: 0;
        }
        
        // Get total paid from all transactions with type income
        $totalPaid = DB::table('transactions')
            ->join('payments', 'transactions.payment_id', '=', 'payments.id')
            ->where('transactions.type', Transaction::TYPE_INCOME)
            ->sum('transactions.amount');
        
        // Calculate remaining debt with correct logic for negative reports
        // If total_amount is negative (refund/adjustment case):
        // Debt = |total_amount| - total_paid
        // If total_amount is positive (normal case):
        // Debt = total_amount - total_paid
        
        if ($totalReported < 0) {
            // Trường hợp hóa đơn âm (điều chỉnh, hoàn trả)
            // Công nợ = |tổng hóa đơn| - đã thanh toán
            $remainingDebt = abs($totalReported) - $totalPaid;
        } else {
            // Trường hợp bình thường
            // Công nợ = tổng hóa đơn - đã thanh toán  
            $remainingDebt = $totalReported - $totalPaid;
        }
        
        return $remainingDebt;
    }
    
    /**
     * Get debt data for each month
     *
     * @param \Illuminate\Support\Collection $months
     * @return array
     */
    private function getDebtByMonth($months)
    {
        $debtData = [];
        
        foreach ($months as $month) {
            $monthStr = $month->format('Y-m');
            $endDate = $month->copy()->endOfMonth()->format('Y-m-d H:i:s');
            
            // Get total reported up to this month
            $totalReported = ShipmentReport::where('monthly', '<=', $monthStr)
                ->sum('total_amount');
            
            // Get total paid up to this month
            $totalPaid = DB::table('transactions')
                ->join('payments', 'transactions.payment_id', '=', 'payments.id')
                ->where('transactions.type', Transaction::TYPE_INCOME)
                ->where('payments.payment_date', '<=', $endDate)
                ->sum('transactions.amount');
            
            // Calculate remaining debt for this month with correct logic
            if ($totalReported < 0) {
                // Trường hợp hóa đơn âm: Công nợ = |tổng hóa đơn| - đã thanh toán
                $remainingDebt = abs($totalReported) - $totalPaid;
            } else {
                // Trường hợp bình thường: Công nợ = tổng hóa đơn - đã thanh toán
                $remainingDebt = $totalReported - $totalPaid;
            }
            
            // Always show positive values in chart (debt amount)
            $debtData[$month->format('m/Y')] = max(0, $remainingDebt);
        }
        
        return $debtData;
    }
}

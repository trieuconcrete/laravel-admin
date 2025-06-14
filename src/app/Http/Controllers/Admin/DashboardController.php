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
        
        // Format chart data for JavaScript
        $chartData = [
            'months' => $lastSixMonths->map(function($date) {
                return $date->format('m/Y');
            })->toArray(),
            'shipmentCounts' => array_values($shipmentCounts),
            'income' => array_values($financialData['income']),
            'expenses' => array_values($financialData['expenses'])
        ];

        return view('admin.dashboard', compact(
            'totalVehicles',
            'totalDrivers',
            'totalCustomers',
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
            $months->push(Carbon::now()->subMonths($i)->startOfMonth());
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
            
            // Calculate income (total value of shipments)
            $monthlyIncome = Shipment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', Shipment::STATUS_CANCELLED)
                ->get()
                ->sum(function($shipment) {
                    return $shipment->distance * $shipment->unit_price + 
                           ($shipment->has_crane_service ? $shipment->crane_price : 0);
                });
            
            // Calculate expenses (sum of expense deductions)
            $monthlyExpenses = ShipmentDeduction::whereHas('shipmentDeductionType', function($query) {
                    $query->where('type', ShipmentDeductionType::TYPE_EXPENSE);
                })
                ->whereHas('shipment', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                    $query->where('status', '!=', Shipment::STATUS_CANCELLED);
                })
                ->sum('amount');
            
            $income[$month->format('m/Y')] = $monthlyIncome;
            $expenses[$month->format('m/Y')] = $monthlyExpenses;
        }
        
        return [
            'income' => $income,
            'expenses' => $expenses
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\ShipmentDeduction;
use App\Models\ShipmentDeductionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month or default to current month
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        list($month, $year) = explode('/', $selectedMonth);
        
        // Get department filter
        $department = $request->get('department');
        
        // Get search query
        $search = $request->get('search');
        
        // Get status filter
        $status = $request->get('status');
        
        // Query users with their position and salary information
        $query = User::with(['position'])->whereNull('deleted_at');
        
        // Exclude current user if authenticated
        if (Auth::check()) {
            $query->where('id', '!=', Auth::id());
        }
        
        // Apply department filter if provided
        if ($department) {
            $query->whereHas('position', function($q) use ($department) {
                $q->where('name', 'like', "%{$department}%");
            });
        }
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        // Get users with pagination
        $users = $query->paginate(10);
        
        // Process salary data for each user
        $salaries = [];
        foreach ($users as $user) {
            // Calculate salary components
            $baseSalary = $user->salary_base ?? 0;
            
            // Get shipments for the user (as driver or co-driver) for the selected month
            $shipments = Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereMonth('departure_time', $month)
                ->whereYear('departure_time', $year)
                ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
                ->orderBy('departure_time')
                ->get();
                
            // Calculate salary details
            $totalAllowance = 0;
            $totalExpenses = 0;
            $shipmentCount = $shipments->count();
            
            // Process shipment deductions for salary calculation
            foreach ($shipments as $shipment) {
                $shipmentAllowance = 0;
                $shipmentAmount = 0;
                
                foreach ($shipment->shipmentDeductions as $deduction) {
                    if ($deduction->user_id == $user->id) {
                        // Check if this is an allowance (driver_and_busboy) or expense
                        if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_DRIVER_AND_BUSBOY) {
                            $shipmentAllowance += $deduction->amount;
                            $totalAllowance += $deduction->amount;
                        } else if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_EXPENSE) {
                            $shipmentAmount += $deduction->amount;
                            $totalExpenses += $deduction->amount; // Add to total expenses
                        }
                    }
                }
            }
            
            // Calculate insurance deduction (10% of total: salary base + allowances + expenses)
            $totalBeforeInsurance = $baseSalary + $totalAllowance + $totalExpenses;
            $insuranceDeduction = $totalBeforeInsurance * 0.1; // 10% of total
            
            // Calculate total salary
            $totalSalary = $totalBeforeInsurance - $insuranceDeduction;
            
            // Determine status (placeholder logic - in real app would come from database)
            $paymentStatus = 'pending';
            if ($user->id % 3 == 0) {
                $paymentStatus = 'paid';
            } elseif ($user->id % 3 == 1) {
                $paymentStatus = 'unpaid';
            }
            
            // Get department name from position
            $departmentName = $user->position ? $user->position->name : 'Chưa phân công';
            
            $salaries[] = [
                'id' => $user->id,
                'user_id' => $user->id,
                'employee_code' => $user->employee_code ?? 'NV' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'name' => $user->full_name,
                'department' => $departmentName,
                'base_salary' => $baseSalary,
                'allowance' => $totalAllowance,
                'shipment_earnings' => $totalExpenses,
                'bonus' => 0, // No bonus calculation in UserController
                'insurance' => $insuranceDeduction,
                'total' => $totalSalary,
                'status' => $paymentStatus,
                'shipment_count' => $shipmentCount
            ];
        }
        
        // Get statistics for dashboard
        $totalEmployees = User::count();
        $totalPaidSalary = array_sum(array_map(function($salary) {
            return $salary['status'] == 'paid' ? $salary['total'] : 0;
        }, $salaries));
        $totalPendingSalary = array_sum(array_map(function($salary) {
            return $salary['status'] == 'pending' ? $salary['total'] : 0;
        }, $salaries));
        $averageSalary = $totalEmployees > 0 ? (array_sum(array_column($salaries, 'total')) / $totalEmployees) : 0;
        
        // Get department statistics
        $departmentStats = [];
        $positions = DB::table('positions')
            ->select(
                'positions.name', 
                'positions.code',
                DB::raw('COUNT(users.id) as count'),
                DB::raw('SUM(users.salary_base) as base_salary_sum')
            )
            ->leftJoin('users', 'positions.id', '=', 'users.position_id')
            ->whereNull('users.deleted_at')
            ->groupBy('positions.id', 'positions.name', 'positions.code')
            ->get();
        
        foreach ($positions as $position) {
            // Get users in this position
            $positionUsers = User::where('position_id', function($query) use ($position) {
                $query->select('id')->from('positions')->where('name', $position->name);
            })->get();
            
            // Calculate total salary for this department using the same logic as individual salaries
            $departmentTotalSalary = 0;
            foreach ($positionUsers as $user) {
                // Get user's shipments for the selected month
                $userShipments = Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->whereMonth('departure_time', $month)
                    ->whereYear('departure_time', $year)
                    ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
                    ->get();
                
                // Calculate user's salary components
                $userBaseSalary = $user->salary_base ?? 0;
                $userAllowance = 0;
                $userExpenses = 0;
                
                foreach ($userShipments as $shipment) {
                    foreach ($shipment->shipmentDeductions as $deduction) {
                        if ($deduction->user_id == $user->id) {
                            if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_DRIVER_AND_BUSBOY) {
                                $userAllowance += $deduction->amount;
                            } else if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_EXPENSE) {
                                $userExpenses += $deduction->amount;
                            }
                        }
                    }
                }
                
                // Calculate insurance and total
                $userTotalBeforeInsurance = $userBaseSalary + $userAllowance + $userExpenses;
                $userInsurance = $userTotalBeforeInsurance * 0.1;
                $userTotal = $userTotalBeforeInsurance - $userInsurance;
                
                $departmentTotalSalary += $userTotal;
            }
            
            $departmentStats[] = [
                'name' => $position->name,
                'code' => $position->code,
                'count' => $position->count,
                'total_salary' => $departmentTotalSalary
            ];
        }
        
        // Generate chart data for the last 6 months
        $chartData = [];
        $currentDate = Carbon::createFromFormat('m/Y', $selectedMonth);
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $currentDate->copy()->subMonths($i);
            $monthKey = $monthDate->format('m/Y');
            $monthLabel = $monthDate->format('m/Y');
            
            // Get total salary for this month
            $monthSalary = 0;
            $monthUsers = User::whereNull('deleted_at')->get();
            
            // Exclude current user if authenticated
            if (Auth::check()) {
                $monthUsers = $monthUsers->filter(function($user) {
                    return $user->id != Auth::id();
                });
            }
            
            foreach ($monthUsers as $user) {
                // Calculate user's salary for this month
                $userMonth = $monthDate->month;
                $userYear = $monthDate->year;
                
                $userShipments = Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->whereMonth('departure_time', $userMonth)
                    ->whereYear('departure_time', $userYear)
                    ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
                    ->get();
                
                $userBaseSalary = $user->salary_base ?? 0;
                $userAllowance = 0;
                $userExpenses = 0;
                
                foreach ($userShipments as $shipment) {
                    foreach ($shipment->shipmentDeductions as $deduction) {
                        if ($deduction->user_id == $user->id) {
                            if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_DRIVER_AND_BUSBOY) {
                                $userAllowance += $deduction->amount;
                            } else if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_EXPENSE) {
                                $userExpenses += $deduction->amount;
                            }
                        }
                    }
                }
                
                $userTotalBeforeInsurance = $userBaseSalary + $userAllowance + $userExpenses;
                $userInsurance = $userTotalBeforeInsurance * 0.1;
                $userTotal = $userTotalBeforeInsurance - $userInsurance;
                
                $monthSalary += $userTotal;
            }
            
            $chartData[] = [
                'month' => $monthLabel,
                'total' => $monthSalary
            ];
        }
        
        return view('admin.salary.index', compact(
            'salaries', 
            'selectedMonth', 
            'users', 
            'totalEmployees', 
            'totalPaidSalary', 
            'totalPendingSalary', 
            'averageSalary',
            'departmentStats',
            'chartData'
        ));
    }
}

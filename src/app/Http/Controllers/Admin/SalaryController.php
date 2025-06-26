<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\SalaryDetail;
use App\Models\SalaryPeriod;
use App\Models\ShipmentDeductionType;
use App\Models\User;
use App\Models\Shipment;
use App\Http\Requests\Salary\SyncSalaryRequest;
use App\Services\SalaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Transaction;
use Carbon\Carbon;

class SalaryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display salary index page with salary data, statistics and charts
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filters from request
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        list($month, $year) = explode('/', $selectedMonth);
        $department = $request->get('department');
        $search = $request->get('search');
        $status = $request->get('status');
        
        // Find salary period for the selected month
        $salaryPeriod = $this->getSalaryPeriodForMonth($month, $year);
        
        // Get filtered users with pagination
        $users = $this->getFilteredUsers($department, $search);
        
        // Process salary data for each user
        $salaries = $this->processSalaryData($users, $salaryPeriod);
        
        // Calculate dashboard statistics
        $dashboardStats = $this->calculateDashboardStatistics($salaries);
        
        // Get department statistics
        $departmentStats = $this->calculateDepartmentStatistics($salaries);
        
        // Generate chart data for the last 6 months
        $chartData = $this->generateChartData($selectedMonth);
        
        return view('admin.salary.index', [
            'salaries' => $salaries, 
            'selectedMonth' => $selectedMonth, 
            'users' => $users, 
            'totalEmployees' => $dashboardStats['totalEmployees'], 
            'totalPaidSalary' => $dashboardStats['totalPaidSalary'], 
            'totalPendingSalary' => $dashboardStats['totalPendingSalary'], 
            'averageSalary' => $dashboardStats['averageSalary'],
            'departmentStats' => $departmentStats,
            'chartData' => $chartData
        ]);
    }
    
    /**
     * Get salary period for a specific month
     *
     * @param string $month
     * @param string $year
     * @return SalaryPeriod|null
     */
    private function getSalaryPeriodForMonth($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        
        return SalaryPeriod::where('start_date', '=', $startDate)
            ->where('end_date', '=', $endDate)
            ->first();
    }
    
    /**
     * Get filtered users with pagination
     *
     * @param string|null $department
     * @param string|null $search
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getFilteredUsers($department, $search)
    {
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
        
        return $query->paginate(10);
    }
    
    /**
     * Process salary data for users
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $users
     * @param SalaryPeriod|null $salaryPeriod
     * @return array
     */
    private function processSalaryData($users, $salaryPeriod)
    {
        $salaries = [];
        
        if (!$salaryPeriod) {
            return $salaries;
        }
        
        foreach ($users as $user) {
            $departmentName = $user->position ? $user->position->name : 'Chưa phân công';
            
            $salaryDetail = SalaryDetail::where('employee_id', $user->id)
                ->where('period_id', $salaryPeriod->period_id)
                ->first();
                
            if (!$salaryDetail) {
                continue;
            }

            $salaries[] = [
                'id' => $user->id,
                'user_id' => $user->id,
                'employee_code' => $user->employee_code ?? 'NV' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'name' => $user->full_name,
                'department' => $departmentName,
                'base_salary' => $salaryDetail->base_salary,
                'allowance' => $salaryDetail->total_allowance,
                'total_expenses' => $salaryDetail->total_expenses,
                'insurance' => $salaryDetail->social_insurance,
                'total' => $salaryDetail->net_salary,
                'status' => $salaryDetail->status,
                'shipment_count' => $salaryDetail->working_days,
                'other_deduction' => $salaryDetail->other_deduction,
                'bonus' => $salaryDetail->bonus,
                'penalty' => $salaryDetail->penalty,
                'total_salary' => $salaryDetail->total_salary
            ];
        }
        
        return $salaries;
    }
    
    /**
     * Calculate dashboard statistics
     *
     * @param array $salaries
     * @return array
     */
    private function calculateDashboardStatistics($salaries)
    {
        $totalEmployees = count($salaries);
        $totalPaidSalary = array_sum(array_map(function($salary) {
            return $salary['status'] == 'paid' ? $salary['total'] : 0;
        }, $salaries));
        $totalPendingSalary = array_sum(array_map(function($salary) {
            return $salary['status'] == 'pending' ? $salary['total'] : 0;
        }, $salaries));
        $averageSalary = $totalEmployees > 0 ? (array_sum(array_column($salaries, 'total')) / $totalEmployees) : 0;
        
        return [
            'totalEmployees' => $totalEmployees,
            'totalPaidSalary' => $totalPaidSalary,
            'totalPendingSalary' => $totalPendingSalary,
            'averageSalary' => $averageSalary
        ];
    }
    
    /**
     * Calculate department statistics
     *
     * @param array $salaries
     * @return array
     */
    private function calculateDepartmentStatistics($salaries)
    {
        // Group salaries by department
        $salariesByDepartment = [];
        foreach ($salaries as $salary) {
            if (!isset($salariesByDepartment[$salary['department']])) {
                $salariesByDepartment[$salary['department']] = [];
            }
            $salariesByDepartment[$salary['department']][] = $salary;
        }
        
        // Get all positions
        $positions = DB::table('positions')
            ->select(
                'positions.name', 
                'positions.code',
                'positions.id as position_id',
                DB::raw('COUNT(users.id) as count'),
                DB::raw('SUM(users.salary_base) as base_salary_sum')
            )
            ->leftJoin('users', 'positions.id', '=', 'users.position_id')
            ->whereNull('users.deleted_at')
            ->groupBy('positions.id', 'positions.name', 'positions.code')
            ->get();
        
        $departmentStats = [];
        foreach ($positions as $position) {
            $departmentName = $position->name;
            $departmentTotalSalary = 0;
            
            if (isset($salariesByDepartment[$departmentName])) {
                $departmentTotalSalary = array_sum(array_column($salariesByDepartment[$departmentName], 'total'));
            }
            
            $departmentStats[] = [
                'name' => $departmentName,
                'code' => $position->code,
                'count' => $position->count,
                'total_salary' => $departmentTotalSalary
            ];
        }
        
        return $departmentStats;
    }
    
    /**
     * Generate chart data for the last 6 months
     *
     * @param string $selectedMonth
     * @return array
     */
    private function generateChartData($selectedMonth)
    {
        $chartData = [];
        $currentDate = Carbon::createFromFormat('m/Y', $selectedMonth);
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $currentDate->copy()->subMonths($i);
            $monthLabel = $monthDate->format('m/Y');
            $monthNum = $monthDate->month;
            $yearNum = $monthDate->year;
            
            $monthSalaryPeriod = $this->getSalaryPeriodForMonth($monthNum, $yearNum);
            $monthSalary = 0;
            
            if ($monthSalaryPeriod) {
                $monthSalary = SalaryDetail::where('period_id', $monthSalaryPeriod->period_id)
                    ->sum('net_salary');
            }
            
            $chartData[] = [
                'month' => $monthLabel,
                'total' => $monthSalary
            ];
        }
        
        return $chartData;
    }
    
    /**
     * Synchronize salary data to SalaryPeriod and SalaryDetail tables
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync(SyncSalaryRequest $request)
    {
        try {
            $salaryService = app(SalaryService::class);
            $result = $salaryService->syncSalary($request->validated());
            
            if ($result['success']) {
                return redirect()->route('admin.salary.index', ['month' => $result['month']])
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Đồng bộ lương thất bại: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process salary payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Process salary payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function processPayment($id)
    {
        // try {
            DB::beginTransaction();

            // Find the salary detail with related data
            $salaryDetail = SalaryDetail::with(['employee', 'salaryPeriod'])->findOrFail($id);
            
            // Check if the authenticated user has permission to process this payment
            // $this->authorize('process', $salaryDetail);
            
            // Validate salary status
            if ($salaryDetail->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lương đã được thanh toán trước đó.'
                ], 400);
            }

            // Validate salary period
            if (!$salaryDetail->salaryPeriod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy kỳ lương tương ứng.'
                ], 400);
            }

            $now = now();
            $adminId = auth('admin')->id();

            // Update salary status to paid
            $salaryDetail->update([
                'status' => 'paid',
                'payment_date' => $now,
                'updated_by' => $adminId,
                'payment_method' => 'bank_transfer' // You can make this dynamic if needed
            ]);

            // Create transaction record
            $transaction = Transaction::create([
                'type' => 'expense',
                'category' => 'salary',
                'amount' => $salaryDetail->net_salary,
                'description' => sprintf(
                    'Thanh toán lương tháng %d/%d cho %s (Mã NV: %s)',
                    $salaryDetail->salaryPeriod->month,
                    $salaryDetail->salaryPeriod->year,
                    $salaryDetail->employee->full_name,
                    $salaryDetail->employee->employee_code
                ),
                'transaction_date' => $now,
                'created_by' => $adminId,
                'reference_id' => $salaryDetail->salary_id,
                'reference_type' => get_class($salaryDetail),
                'metadata' => [
                    'employee_id' => $salaryDetail->employee_id,
                    'employee_name' => $salaryDetail->employee->full_name,
                    'period' => $salaryDetail->salaryPeriod->month . '/' . $salaryDetail->salaryPeriod->year,
                    'base_salary' => $salaryDetail->base_salary,
                    'total_allowances' => $salaryDetail->total_allowances ?? 0,
                    'total_deductions' => $salaryDetail->total_deductions ?? 0,
                    'net_salary' => $salaryDetail->net_salary
                ]
            ]);

            // Log the payment
            Log::info('Salary payment processed', [
                'salary_id' => $salaryDetail->salary_id,
                'employee_id' => $salaryDetail->employee_id,
                'amount' => $salaryDetail->net_salary,
                'processed_by' => $adminId,
                'transaction_id' => $transaction->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán lương thành công.',
                'data' => [
                    'payment_date' => $now->format('d/m/Y H:i:s'),
                    'transaction_id' => $transaction->id,
                    'amount' => number_format($salaryDetail->net_salary) . ' VNĐ'
                ]
            ]);

        // } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        //     DB::rollBack();
        //     Log::error('Salary record not found: ' . $e->getMessage());
            
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Không tìm thấy bản ghi lương.'
        //     ], 404);
            
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Error processing salary payment: ' . $e->getMessage());
            
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Đã xảy ra lỗi khi xử lý thanh toán. Vui lòng thử lại sau.'
        //     ], 500);
        // }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\SalaryDetail;
use App\Models\SalaryPeriod;
use App\Models\ShipmentDeductionType;
use App\Models\User;
use App\Models\Shipment;
use App\Models\SalaryAdvanceRequest;
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

    protected $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

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
        // dd($users);
        // Process salary data for each user
        $salaries = $this->processSalaryData($users, $salaryPeriod);
        
        // dd($salaries);
        // Calculate dashboard statistics
        $dashboardStats = $this->calculateDashboardStatistics($salaries);
        
        // Get department statistics
        $departmentStats = $this->calculateDepartmentStatistics($salaries);
        
        // Get salary statistics by type
        $salaryStatsByType = $this->getSalaryStatisticsByType($salaries);
        
        // Generate chart data for the last 6 months
        $chartData = $this->generateChartData($selectedMonth);
        
        // Get pending salary advance requests for approval
        $pendingAdvanceRequests = $this->getPendingAdvanceRequests();
        
        return view('admin.salary.index', [
            'salaries' => $salaries, 
            'selectedMonth' => $selectedMonth, 
            'users' => $users, 
            'totalEmployees' => $dashboardStats['totalEmployees'], 
            'totalPaidSalary' => $dashboardStats['totalPaidSalary'], 
            'totalPendingSalary' => $dashboardStats['totalPendingSalary'], 
            'averageSalary' => $dashboardStats['averageSalary'],
            'basicSalaryCount' => $dashboardStats['basicSalaryCount'],
            'commissionSalaryCount' => $dashboardStats['commissionSalaryCount'],
            'totalCommission' => $dashboardStats['totalCommission'],
            'totalTripValue' => $dashboardStats['totalTripValue'],
            'totalTrips' => $dashboardStats['totalTrips'],
            'departmentStats' => $departmentStats,
            'salaryStatsByType' => $salaryStatsByType,
            'chartData' => $chartData,
            'pendingAdvanceRequests' => $pendingAdvanceRequests
        ]);
    }
    
    /**
     * Get salary period for a specific month
     * Sử dụng logic tính ngày mới từ SalaryService (issue #197)
     *
     * @param string $month
     * @param string $year
     * @return SalaryPeriod|null
     */
    private function getSalaryPeriodForMonth($month, $year)
    {
        // Sử dụng logic tính ngày mới từ SalaryService
        $periodDates = $this->salaryService->calculateSalaryPeriodDates((int)$month, (int)$year);
        
        // Tìm salary period theo ngày đã tính từ settings
        return SalaryPeriod::where('start_date', '=', $periodDates['start_date']->format('Y-m-d'))
            ->where('end_date', '=', $periodDates['end_date']->format('Y-m-d'))
            ->first();
    }

    /**
     * Get month/year from salary period
     * Lấy tháng/năm chính xác từ kỳ lương (sử dụng end_date)
     *
     * @param SalaryPeriod $salaryPeriod
     * @return array
     */
    private function getMonthYearFromSalaryPeriod($salaryPeriod)
    {
        $endDate = Carbon::parse($salaryPeriod->end_date);
        return [
            'month' => $endDate->month,
            'year' => $endDate->year,
            'formatted' => sprintf('%02d/%d', $endDate->month, $endDate->year)
        ];
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
        
        return $query->paginate(15);
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

            // Xử lý theo loại lương của user
            $salaryType = $user->salary_type?->value ?? 1; // 1: BASIC_SALARY, 2: COMMISSION_SALARY
            $salaryTypeLabel = $user->salary_type?->getLabel() ?? 'Lương cơ bản';
            
            // Sử dụng data đã được tính sẵn trong SalaryDetail từ sync
            // Chỉ tính thêm thông tin commission và trip cho display
            $additionalInfo = $this->getAdditionalSalaryInfo($user, $salaryDetail, $salaryType);
            
            $salaries[] = [
                'id' => $user->id,
                'user_id' => $user->id,
                'employee_code' => $user->employee_code ?? 'NV' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'name' => $user->full_name,
                'department' => $departmentName,
                'salary_type' => $salaryType,
                'salary_type_label' => $salaryTypeLabel,
                'base_salary' => $salaryDetail->base_salary, // Sử dụng data đã sync
                'allowance' => $salaryDetail->total_allowance,
                'total_expenses' => $salaryDetail->total_expenses,
                'insurance' => $salaryDetail->social_insurance,
                'total' => max(0, $salaryDetail->net_salary), // Đảm bảo không âm
                'status' => $salaryDetail->status,
                'shipment_count' => $salaryDetail->working_days,
                'other_deduction' => $salaryDetail->other_deduction,
                'bonus' => $salaryDetail->bonus,
                'penalty' => $salaryDetail->penalty,
                'total_salary' => $salaryDetail->total_salary, // Sử dụng data đã sync
                'commission_amount' => $additionalInfo['commission_amount'],
                'trip_count' => $additionalInfo['trip_count'],
                'total_trip_value' => $additionalInfo['total_trip_value']
            ];
        }
        
        return $salaries;
    }
    
    /**
     * Calculate salary by type (Basic or Commission)
     *
     * @param User $user
     * @param SalaryDetail $salaryDetail
     * @param int $salaryType
     * @return array
     */
    private function calculateSalaryByType($user, $salaryDetail, $salaryType)
    {
        // Lấy thông tin kỳ lương
        $periodDates = $this->salaryService->calculateSalaryPeriodDates(
            Carbon::parse($salaryDetail->salaryPeriod->end_date)->month,
            Carbon::parse($salaryDetail->salaryPeriod->end_date)->year
        );
        
        if ($salaryType == 2) { // COMMISSION_SALARY - Lương doanh số
            // Tài xế lương doanh số: tính 12% commission trên tổng giá trị chuyến xe
            $shipments = Shipment::where(function($query) use ($user) {
                    $query->where('driver_id', $user->id)
                          ->orWhere('co_driver_id', $user->id);
                })
                ->whereBetween('run_date', [$periodDates['start_date'], $periodDates['end_date']])
                ->where('status', 'completed')
                ->get();
            
            // Tính tổng giá trị chuyến xe: sum(unit_price * trip_count)
            $totalTripValue = 0;
            $tripCount = $shipments->count();
            
            foreach ($shipments as $shipment) {
                $unitPrice = $shipment->unit_price ?? 0;
                $tripCountPerShipment = $shipment->trip_count ?? 1;
                $totalTripValue += ($unitPrice * $tripCountPerShipment);
            }
            
            // Tính lương cơ bản = 12% của tổng giá trị chuyến xe
            $commissionRate = 0.12; // 12%
            $baseSalary = $totalTripValue * $commissionRate;
            $commissionAmount = $baseSalary; // Commission amount = base salary cho loại này
            
            // Tính tổng lương và lương thực nhận
            $totalSalary = $baseSalary + ($salaryDetail->total_allowance ?? 0) - ($salaryDetail->total_deductions ?? 0);
            $netSalary = $totalSalary - ($salaryDetail->social_insurance ?? 0);
            
            return [
                'base_salary' => $baseSalary,
                'total_salary' => $totalSalary,
                'net_salary' => $netSalary,
                'commission_amount' => $commissionAmount,
                'trip_count' => $tripCount,
                'total_trip_value' => $totalTripValue
            ];
        } else { // BASIC_SALARY - Lương cơ bản
            // Tài xế lương cơ bản: sử dụng salary_base từ users table
            $baseSalary = $user->salary_base ?? 0;
            
            // Tính tổng lương và lương thực nhận
            $totalSalary = $baseSalary + ($salaryDetail->total_allowance ?? 0) - ($salaryDetail->total_deductions ?? 0);
            $netSalary = $totalSalary - ($salaryDetail->social_insurance ?? 0);
            
            return [
                'base_salary' => $baseSalary,
                'total_salary' => $totalSalary,
                'net_salary' => $netSalary,
                'commission_amount' => 0,
                'trip_count' => 0,
                'total_trip_value' => 0
            ];
        }
    }
    
    /**
     * Get additional salary info for display (commission, trip count, total trip value)
     *
     * @param User $user
     * @param SalaryDetail $salaryDetail
     * @param int $salaryType
     * @return array
     */
    private function getAdditionalSalaryInfo($user, $salaryDetail, $salaryType)
    {
        if ($salaryType == 2) { // COMMISSION_SALARY
            // Lấy thông tin kỳ lương
            $periodDates = $this->salaryService->calculateSalaryPeriodDates(
                Carbon::parse($salaryDetail->salaryPeriod->end_date)->month,
                Carbon::parse($salaryDetail->salaryPeriod->end_date)->year
            );
            
            // Tìm shipments của user trong kỳ lương
            $shipments = Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where(function($query) use ($periodDates) {
                    $query->where(function($subQuery) use ($periodDates) {
                        $subQuery->whereNotNull('run_date')
                                 ->whereBetween('run_date', [$periodDates['start_date'], $periodDates['end_date']]);
                    })
                    ->orWhere(function($subQuery) use ($periodDates) {
                        $subQuery->whereNull('run_date')
                                 ->whereBetween('departure_time', [$periodDates['start_date'], $periodDates['end_date']]);
                    });
                })
                ->where('status', 'completed')
                ->get();
            
            // Tính tổng giá trị chuyến xe
            $totalTripValue = 0;
            foreach ($shipments as $shipment) {
                $unitPrice = $shipment->unit_price ?? 0;
                $tripCount = $shipment->trip_count ?? 1;
                $totalTripValue += ($unitPrice * $tripCount);
            }
            
            $commissionAmount = $salaryDetail->base_salary; // Commission = base salary cho loại này
            $tripCount = $shipments->count();
            
            return [
                'commission_amount' => $commissionAmount,
                'trip_count' => $tripCount,
                'total_trip_value' => $totalTripValue
            ];
        } else { // BASIC_SALARY
            return [
                'commission_amount' => 0,
                'trip_count' => 0,
                'total_trip_value' => 0
            ];
        }
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
        
        // Thống kê theo loại lương
        $basicSalaryCount = count(array_filter($salaries, function($salary) {
            return $salary['salary_type'] == 1; // BASIC_SALARY
        }));
        $commissionSalaryCount = count(array_filter($salaries, function($salary) {
            return $salary['salary_type'] == 2; // COMMISSION_SALARY
        }));
        
        // Tổng commission và trip value
        $totalCommission = array_sum(array_column($salaries, 'commission_amount'));
        $totalTripValue = array_sum(array_column($salaries, 'total_trip_value'));
        $totalTrips = array_sum(array_column($salaries, 'trip_count'));
        
        return [
            'totalEmployees' => $totalEmployees,
            'totalPaidSalary' => $totalPaidSalary,
            'totalPendingSalary' => $totalPendingSalary,
            'averageSalary' => $averageSalary,
            'basicSalaryCount' => $basicSalaryCount,
            'commissionSalaryCount' => $commissionSalaryCount,
            'totalCommission' => $totalCommission,
            'totalTripValue' => $totalTripValue,
            'totalTrips' => $totalTrips
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
            $departmentBasicSalary = 0;
            $departmentCommissionSalary = 0;
            $departmentTotalCommission = 0;
            $departmentTotalTrips = 0;
            
            if (isset($salariesByDepartment[$departmentName])) {
                $departmentSalaries = $salariesByDepartment[$departmentName];
                $departmentTotalSalary = array_sum(array_column($departmentSalaries, 'total'));
                
                // Phân loại theo loại lương
                foreach ($departmentSalaries as $salary) {
                    if ($salary['salary_type'] == 1) { // BASIC_SALARY
                        $departmentBasicSalary += $salary['total'];
                    } else if ($salary['salary_type'] == 2) { // COMMISSION_SALARY
                        $departmentCommissionSalary += $salary['total'];
                        $departmentTotalCommission += $salary['commission_amount'];
                        $departmentTotalTrips += $salary['trip_count'];
                    }
                }
            }
            
            $departmentStats[] = [
                'name' => $departmentName,
                'code' => $position->code,
                'count' => $position->count,
                'total_salary' => $departmentTotalSalary,
                'basic_salary' => $departmentBasicSalary,
                'commission_salary' => $departmentCommissionSalary,
                'total_commission' => $departmentTotalCommission,
                'total_trips' => $departmentTotalTrips
            ];
        }
        
        return $departmentStats;
    }
    
    /**
     * Get salary statistics by type
     *
     * @param array $salaries
     * @return array
     */
    private function getSalaryStatisticsByType($salaries)
    {
        $basicSalaryStats = [
            'count' => 0,
            'total_salary' => 0,
            'average_salary' => 0
        ];
        
        $commissionSalaryStats = [
            'count' => 0,
            'total_salary' => 0,
            'average_salary' => 0,
            'total_commission' => 0,
            'total_trip_value' => 0,
            'total_trips' => 0
        ];
        
        foreach ($salaries as $salary) {
            if ($salary['salary_type'] == 1) { // BASIC_SALARY
                $basicSalaryStats['count']++;
                $basicSalaryStats['total_salary'] += $salary['total'];
            } else if ($salary['salary_type'] == 2) { // COMMISSION_SALARY
                $commissionSalaryStats['count']++;
                $commissionSalaryStats['total_salary'] += $salary['total'];
                $commissionSalaryStats['total_commission'] += $salary['commission_amount'];
                $commissionSalaryStats['total_trip_value'] += $salary['total_trip_value'];
                $commissionSalaryStats['total_trips'] += $salary['trip_count'];
            }
        }
        
        // Tính average salary
        if ($basicSalaryStats['count'] > 0) {
            $basicSalaryStats['average_salary'] = $basicSalaryStats['total_salary'] / $basicSalaryStats['count'];
        }
        
        if ($commissionSalaryStats['count'] > 0) {
            $commissionSalaryStats['average_salary'] = $commissionSalaryStats['total_salary'] / $commissionSalaryStats['count'];
        }
        
        return [
            'basic_salary' => $basicSalaryStats,
            'commission_salary' => $commissionSalaryStats
        ];
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
                // Sử dụng logic mới: đảm bảo không âm
                $monthSalary = SalaryDetail::where('period_id', $monthSalaryPeriod->period_id)
                    ->get()
                    ->sum(function($detail) {
                        return max(0, $detail->net_salary);
                    });
            }
            
            $chartData[] = [
                'month' => $monthLabel,
                'total' => $monthSalary
            ];
            
            // Debug log cho tháng 8/2025
            if ($monthLabel === '08/2025') {
                Log::info('Chart Data Debug - Tháng 8/2025', [
                    'month' => $monthLabel,
                    'monthSalary' => $monthSalary,
                    'monthSalaryPeriod' => $monthSalaryPeriod ? $monthSalaryPeriod->period_id : null,
                    'expected_total' => 569510000
                ]);
            }
        }
        
        return $chartData;
    }
    
    /**
     * Get pending salary advance requests for approval
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getPendingAdvanceRequests()
    {
        $perPage = request('per_page', 10);
        
        return SalaryAdvanceRequest::with(['user'])
            ->where('status', SalaryAdvanceRequest::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function processPayment($id)
    {
        try {
            DB::beginTransaction();

            // Find the salary detail with related data
            $salaryDetail = SalaryDetail::with(['employee', 'salaryPeriod'])->findOrFail($id);
            $netSalary = $salaryDetail->net_salary;
            
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

            // Check if this is a repeated payment
            $isRepeatedPayment = $salaryDetail->status === 'paid';
            
            // Update salary status to paid (allow multiple payments)
            $salaryDetail->update([
                'status' => 'paid',
                'payment_date' => $now,
                'updated_by' => $adminId,
                'payment_method' => 'bank_transfer' // You can make this dynamic if needed
            ]);

            // Lấy tháng/năm chính xác từ kỳ lương
            $periodInfo = $this->getMonthYearFromSalaryPeriod($salaryDetail->salaryPeriod);
            $salaryMonth = $periodInfo['month'];
            $salaryYear = $periodInfo['year'];
            
            // Lấy thông tin loại lương
            $salaryType = $salaryDetail->employee->salary_type?->value ?? 1;
            $salaryTypeLabel = $salaryDetail->employee->salary_type?->getLabel() ?? 'Lương cơ bản';
            
            // Tạo description theo loại lương
            $salaryDescription = $salaryType == 2 
                ? sprintf('%s lương doanh số tháng %d/%d cho %s (Mã NV: %s)', 
                    $isRepeatedPayment ? 'Thanh toán lại' : 'Thanh toán',
                    $salaryMonth, $salaryYear,
                    $salaryDetail->employee->full_name,
                    $salaryDetail->employee->employee_code)
                : sprintf('%s lương cơ bản tháng %d/%d cho %s (Mã NV: %s)', 
                    $isRepeatedPayment ? 'Thanh toán lại' : 'Thanh toán',
                    $salaryMonth, $salaryYear,
                    $salaryDetail->employee->full_name,
                    $salaryDetail->employee->employee_code);
            
            // Create transaction record
            $transaction = Transaction::create([
                'type' => 'expense',
                'category' => 'salary',
                'amount' => $netSalary,
                'description' => $salaryDescription,
                'transaction_date' => Carbon::parse($salaryDetail->salaryPeriod->end_date)->addDays(1),
                'created_by' => $adminId,
                'reference_id' => $salaryDetail->salary_id,
                'reference_type' => get_class($salaryDetail),
                'metadata' => [
                    'employee_id' => $salaryDetail->employee_id,
                    'employee_name' => $salaryDetail->employee->full_name,
                    'period' => sprintf('%02d/%d', $salaryMonth, $salaryYear),
                    'salary_type' => $salaryType,
                    'salary_type_label' => $salaryTypeLabel,
                    'base_salary' => $salaryDetail->base_salary,
                    'total_allowances' => $salaryDetail->total_allowances ?? 0,
                    'total_deductions' => $salaryDetail->total_deductions ?? 0,
                    'net_salary' => $netSalary,
                    'is_repeated_payment' => $isRepeatedPayment
                ],
                'payment_id' => null // Set to null since this is a salary payment, not a customer payment
            ]);

            // Log the payment
            Log::info('Salary payment processed', [
                'salary_id' => $salaryDetail->salary_id,
                'employee_id' => $salaryDetail->employee_id,
                'net_salary' => $netSalary,
                'processed_by' => $adminId,
                'transaction_id' => $transaction->id,
                'is_repeated_payment' => $isRepeatedPayment
            ]);
            
            /** process sync salary */
            $monthRequest = sprintf('%02d/%d', $salaryMonth, $salaryYear);
            // Prepare sync data
            $dataSync = [
                'month' => $monthRequest,
                'period_name' => 'Kỳ lương tháng ' . $monthRequest
            ];
            // Sync salary
            $salaryService = app(SalaryService::class);
            $result = $salaryService->syncSalary($dataSync);

            if (!$result['success']) {
                throw new \Exception($result['message']);   
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isRepeatedPayment ? 'Thanh toán lại lương thành công.' : 'Thanh toán lương thành công.',
                'data' => [
                    'payment_date' => $now->format('d/m/Y H:i:s'),
                    'transaction_id' => $transaction->id,
                    'amount' => number_format($salaryDetail->net_salary) . ' VNĐ',
                    'is_repeated_payment' => $isRepeatedPayment
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Salary record not found: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bản ghi lương.'
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing salary payment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý thanh toán. Vui lòng thử lại sau.'
            ], 500);
        }
    }
    
    /**
     * Approve or reject salary advance request
     *
     * @param Request $request
     * @param int $requestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function processAdvanceRequest(Request $request, $requestId)
    {
        try {
            Log::info('processAdvanceRequest called', [
                'requestId' => $requestId,
                'requestData' => $request->all(),
                'jsonData' => $request->json()->all()
            ]);
            
            DB::beginTransaction();
            
            $advanceRequest = SalaryAdvanceRequest::with(['user'])->findOrFail($requestId);
            $action = $request->input('action'); // 'approve' or 'reject'
            $notes = $request->input('notes', '');
            
            // If action is not in request body, try to get from JSON
            if (!$action) {
                $jsonData = $request->json()->all();
                $action = $jsonData['action'] ?? null;
                $notes = $jsonData['notes'] ?? '';
            }
            
            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hành động không hợp lệ'
                ], 400);
            }
            
            $adminId = auth('admin')->id();
            
            if ($action === 'approve') {
                $advanceRequest->update([
                    'status' => SalaryAdvanceRequest::STATUS_APPROVED,
                    'updated_by' => $adminId
                ]);
                
                $message = 'Đã duyệt yêu cầu ứng lương thành công';
            } else {
                $advanceRequest->update([
                    'status' => SalaryAdvanceRequest::STATUS_REJECTED,
                    'updated_by' => $adminId
                ]);
                
                $message = 'Đã từ chối yêu cầu ứng lương';
            }
            
            // Log the action
            Log::info('Salary advance request processed', [
                'request_id' => $advanceRequest->id,
                'action' => $action,
                'processed_by' => $adminId,
                'notes' => $notes
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $advanceRequest->id,
                    'status' => $advanceRequest->status,
                    'status_label' => $advanceRequest->status_label,
                    'status_color' => $advanceRequest->status_color
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing salary advance request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý yêu cầu. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}

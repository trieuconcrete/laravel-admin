<?php

namespace App\Services;

use App\Models\User;
use App\Models\Shipment;
use App\Models\ShipmentDeductionType;
use App\Models\SalaryPeriod;
use App\Models\SalaryAdvanceRequest;
use App\Repositories\Interface\SalaryPeriodRepositoryInterface;
use App\Repositories\Interface\SalaryDetailRepositoryInterface;
use App\Repositories\Interface\ShipmentRepositoryInterface;
use App\Repositories\Interface\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Constants;
use Illuminate\Support\Facades\Log;

class SalaryService
{
    protected $salaryPeriodRepository;
    protected $salaryDetailRepository;
    protected $shipmentRepository;
    protected $userRepository;
    
    /**
     * Constructor
     * 
     * @param SalaryPeriodRepositoryInterface $salaryPeriodRepository
     * @param SalaryDetailRepositoryInterface $salaryDetailRepository
     * @param UserRepositoryInterface $userRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        SalaryPeriodRepositoryInterface $salaryPeriodRepository,
        SalaryDetailRepositoryInterface $salaryDetailRepository,
        UserRepositoryInterface $userRepository,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->salaryPeriodRepository = $salaryPeriodRepository;
        $this->salaryDetailRepository = $salaryDetailRepository;
        $this->userRepository = $userRepository;
    }
    
    /**
     * Synchronize salary data
     * 
     * @param array $data
     * @return array
     */
    public function syncSalary(array $data)
    {
        // Parse month/year
        list($month, $year) = explode('/', $data['month']);
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Create or update salary period
            $salaryPeriod = $this->createOrUpdateSalaryPeriod($data, $month, $year);
            
            // Get all users
            $users = $this->userRepository->all();
            
            // Process salary data for each user
            foreach ($users as $user) {
                $this->processSalaryForUser($user, $salaryPeriod, $month, $year);
            }
            
            // Commit transaction
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Đã đồng bộ dữ liệu lương thành công!',
                'month' => $data['month']
            ];
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sync salary for a specific user
     * 
     * @param User $user
     * @param string $month Format: m/Y (e.g., 06/2025)
     * @return array
     */
    public function syncSalaryForUser(User $user, string $month)
    {
        // Parse month/year
        list($monthNum, $year) = explode('/', $month);
        
        // Log the start of sync process
        Log::info('Starting salary sync for user', [
            'user_id' => $user->id,
            'user_name' => $user->full_name,
            'month' => $month,
            'month_num' => $monthNum,
            'year' => $year
        ]);
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Create or update salary period
            $periodName = 'Kỳ lương tháng ' . $month;
            $salaryPeriod = $this->createOrUpdateSalaryPeriod([
                'period_name' => $periodName,
                // 'notes' => 'Tự động đồng bộ cho nhân viên mới'
            ], $monthNum, $year);
            
            Log::info('Salary period created/updated', [
                'period_id' => $salaryPeriod->period_id ?? 'unknown',
                'period_name' => $periodName
            ]);
            
            // Process salary data for the specific user and get salary detail
            $salaryDetail = $this->processSalaryForUser($user, $salaryPeriod, $monthNum, $year);
            
            // Commit transaction
            DB::commit();
            
            Log::info('Salary sync completed successfully', [
                'user_id' => $user->id,
                'month' => $month,
                'salary_detail_id' => $salaryDetail->salary_id ?? null
            ]);
            
            return [
                'success' => true,
                'message' => 'Đã đồng bộ dữ liệu lương cho nhân viên thành công!',
                'month' => $month,
                'user_id' => $user->id,
                'salary_detail_id' => $salaryDetail->salary_id ?? null
            ];
                
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('Salary sync failed', [
                'user_id' => $user->id,
                'month' => $month,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create or update salary period
     * 
     * @param array $data
     * @param string $month
     * @param string $year
     * @return mixed
     */
    protected function createOrUpdateSalaryPeriod(array $data, $month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $paymentDate = $endDate->copy()->addDays(10); // Payment date is 10 days after end of month
        
        // Check if period already exists
        $salaryPeriod = $this->salaryPeriodRepository->findSalaryPeriodByCondition(['period_name' => $data['period_name']]);
        
        if (!$salaryPeriod) {
            // Create new salary period
            $salaryPeriod = $this->salaryPeriodRepository->create([
                'period_name' => $data['period_name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
                'status' => 'processing',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
        }
        
        return $salaryPeriod;
    }
    
    /**
     * Process salary for a user
     * 
     * @param User $user
     * @param mixed $salaryPeriod
     * @param string $month
     * @param string $year
     * @return \App\Models\SalaryDetail
     */
    protected function processSalaryForUser(User $user, $salaryPeriod, $month, $year)
    {
        // Calculate salary components
        $baseSalary = $user->salary_base ?? 0;
        
        // Get completed shipments for the user (as driver or co-driver) for the selected month
        $shipments = $this->shipmentRepository->getUserCompletedShipments($user, $month, $year);
            
        // Calculate salary details
        $salaryDetails = $this->calculateSalaryDetails($user, $shipments, $salaryPeriod);

        // Check if salary detail already exists and is paid
        $existingSalaryDetail = \App\Models\SalaryDetail::where('employee_id', $user->id)
            ->where('period_id', $salaryPeriod->period_id)
            ->first();

        $updateData = [
            'base_salary' => $baseSalary, // lương cơ bản
            'working_days' => 0,
            'total_expenses' => 0, // Không tính chi phí chuyến hàng
            'total_allowance' => $salaryDetails['totalAllowance'], // phụ cấp
            'social_insurance' => $salaryDetails['insuranceDeduction'], // thuế
            'health_insurance' => 0, // Not used in current calculation
            'income_tax' => Constants::TAX_IN_VAT, // Not used in current calculation
            'total_salary' => $salaryDetails['totalBeforeInsurance'], // lương trước thuế
            'net_salary' => $salaryDetails['totalSalary'], // lương thực tế
            'bonus' => $salaryDetails['totalTypeBonus'], // No bonus calculation
            'penalty' => $salaryDetails['totalTypePenalty'], // No penalty calculation
            'other_deduction' => $salaryDetails['totalTypeSalary'], // Not used in current calculation
        ];

        // Only update status if not already paid
        if (!$existingSalaryDetail || $existingSalaryDetail->status !== 'paid') {
            $updateData['status'] = 'pending';
            $updateData['payment_date'] = null;
            $updateData['payment_method'] = null;
        }

        return $this->salaryDetailRepository->updateOrCreate(
            [
                'employee_id' => $user->id,
                'period_id' => $salaryPeriod->period_id
            ],
            $updateData
        );
    }

    /** */
    protected function calculateSalaryDetails(User $user, Collection $shipments, $salaryPeriod)
    {
        $totalAllowance = 0;
        $totalExpenses = 0; // Không tính chi phí chuyến hàng

        $totalTypeSalary = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypeBonus = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypePenalty = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        
        // Process shipment deductions for salary calculation - chỉ tính cho shipment đã hoàn thành
        foreach ($shipments as $shipment) {
            $totalAllowance += $shipment->shipmentDeductionTypeDriverAndBusboy($user->id)->sum('amount') ?? 0; // tổng phụ cấp
            // Không tính chi phí chuyến hàng vào lương nhân viên
            // $totalExpenses += $shipment->shipmentDeductionTypeExpense()->sum('amount') ?? 0; // tổng chi phí
        }
        
        // Calculate insurance deduction (10% of total: salary base + allowances)
        $baseSalary = $user->salary_base ?? 0;
        $totalBeforeInsurance = ($baseSalary + $totalAllowance + $totalTypeBonus) - ($totalTypeSalary + $totalTypePenalty);
        $insuranceDeduction = $totalBeforeInsurance * (Constants::TAX_IN_VAT/100); // 10% of total
        
        // Calculate total salary - updated formula
        $totalSalary = $totalBeforeInsurance - $insuranceDeduction;
        
        return [
            'totalAllowance' => $totalAllowance,
            'totalExpenses' => $totalExpenses,
            'totalBeforeInsurance' => $totalBeforeInsurance,
            'insuranceDeduction' => $insuranceDeduction,
            'totalSalary' => $totalSalary,
            'totalTypeSalary' => $totalTypeSalary,
            'totalTypeBonus' => $totalTypeBonus,
            'totalTypePenalty' => $totalTypePenalty
        ];
    }
}
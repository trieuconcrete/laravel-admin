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
use App\Enum\SalaryType;
use Illuminate\Support\Facades\Log;

class SalaryService
{
    protected $salaryPeriodRepository;
    protected $salaryDetailRepository;
    protected $shipmentRepository;
    protected $userRepository;
    protected $settingService;
    
    /**
     * Constructor
     * 
     * @param SalaryPeriodRepositoryInterface $salaryPeriodRepository
     * @param SalaryDetailRepositoryInterface $salaryDetailRepository
     * @param UserRepositoryInterface $userRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SettingService $settingService
     */
    public function __construct(
        SalaryPeriodRepositoryInterface $salaryPeriodRepository,
        SalaryDetailRepositoryInterface $salaryDetailRepository,
        UserRepositoryInterface $userRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        SettingService $settingService
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->salaryPeriodRepository = $salaryPeriodRepository;
        $this->salaryDetailRepository = $salaryDetailRepository;
        $this->userRepository = $userRepository;
        $this->settingService = $settingService;
    }
    
    /**
     * Calculate salary period dates based on settings
     * Tính ngày bắt đầu và kết thúc kỳ lương theo yêu cầu issue #197
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function calculateSalaryPeriodDates(int $month, int $year): array
    {
        // Lấy cấu hình từ settings với cache
        $startDay = (int) $this->settingService->get('salary_start_date', 26);
        $endDay = (int) $this->settingService->get('salary_end_date', 25);
        
        // Tính ngày bắt đầu: 26 tháng trước
        $startDate = Carbon::create($year, $month - 1, $startDay);
        
        // Tính ngày kết thúc: 25 tháng sau
        $endDate = Carbon::create($year, $month, $endDay);
        
        // Đảm bảo ngày kết thúc sau ngày bắt đầu
        if ($endDate->lte($startDate)) {
            $endDate->addMonth();
        }
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period_name' => sprintf('%02d/%d', $month, $year)
        ];
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
        // Calculate salary period dates based on settings (issue #197)
        $periodDates = $this->calculateSalaryPeriodDates((int)$month, (int)$year);
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];
        $paymentDate = $endDate->copy()->addDays(10); // Payment date is 10 days after end of month
        
        // Check if period already exists
        $salaryPeriod = $this->salaryPeriodRepository->findSalaryPeriodByCondition(['period_name' => $data['period_name']]);
        
        if (!$salaryPeriod) {
            // Create new salary period with calculated dates
            $salaryPeriod = $this->salaryPeriodRepository->create([
                'period_name' => $data['period_name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
                'status' => 'processing',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
        } else {
            // Update existing period with new calculated dates
            $salaryPeriod = $this->salaryPeriodRepository->update($salaryPeriod->period_id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
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
        // Calculate salary period dates based on settings
        $periodDates = $this->calculateSalaryPeriodDates((int)$month, (int)$year);
        
        // Get completed shipments for the user in the calculated period
        $shipments = $this->getUserCompletedShipmentsInPeriod($user, $periodDates['start_date'], $periodDates['end_date']);
        
        // Calculate salary details based on salary type
        $salaryDetails = $this->calculateSalaryDetailsByType($user, $shipments, $salaryPeriod);

        // Check if salary detail already exists and is paid
        $existingSalaryDetail = \App\Models\SalaryDetail::where('employee_id', $user->id)
            ->where('period_id', $salaryPeriod->period_id)
            ->first();

        $updateData = [
            'base_salary' => $salaryDetails['baseSalary'],
            'working_days' => 0,
            'total_expenses' => 0,
            'total_allowance' => $salaryDetails['totalAllowance'],
            'social_insurance' => $salaryDetails['socialInsurance'],
            'health_insurance' => 0,
            'income_tax' => 0,
            'total_salary' => $salaryDetails['totalSalary'],
            'net_salary' => $salaryDetails['netSalary'],
            'bonus' => $salaryDetails['totalTypeBonus'],
            'penalty' => $salaryDetails['totalTypePenalty'],
            'other_deduction' => $salaryDetails['totalTypeSalary'],
            'salary_type' => $user->salary_type?->value ?? SalaryType::BASIC_SALARY->value,
            'salary_by_percent' => $user->isCommissionSalaryType() ? $user->getSalaryByPercent() : null,
        ];

        // Always allow status updates for repeated payments
        if (!$existingSalaryDetail) {
            $updateData['status'] = 'pending';
            $updateData['payment_date'] = null;
            $updateData['payment_method'] = null;
        }
        // If existing and paid, keep the paid status and payment info

        return $this->salaryDetailRepository->updateOrCreate(
            [
                'employee_id' => $user->id,
                'period_id' => $salaryPeriod->period_id
            ],
            $updateData
        );
    }

    /**
     * Get completed shipments for a user in a specific date period
     * 
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    protected function getUserCompletedShipmentsInPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        return Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            // ->where(function($query) use ($startDate, $endDate) {
            //     // Ưu tiên run_date, fallback về departure_time nếu run_date null
            //     $query->where(function($subQuery) use ($startDate, $endDate) {
            //         $subQuery->whereNotNull('run_date')
            //                  ->whereBetween('run_date', [$startDate, $endDate]);
            //     })
            //     ->orWhere(function($subQuery) use ($startDate, $endDate) {
            //         $subQuery->whereNull('run_date')
                             ->whereBetween('departure_time', [$startDate, $endDate])
            //     });
            // })
            ->completed() // Chỉ lấy shipment đã hoàn thành
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('run_date')
            ->orderBy('departure_time')
            ->get();
    }

    /**
     * Calculate salary details based on salary type
     * 
     * @param User $user
     * @param Collection $shipments
     * @param mixed $salaryPeriod
     * @return array
     */
    protected function calculateSalaryDetailsByType(User $user, Collection $shipments, $salaryPeriod): array
    {
        $salaryType = $user->salary_type?->value ?? SalaryType::BASIC_SALARY->value;
        
        if ($salaryType == SalaryType::COMMISSION_SALARY->value) {
            // Tài xế ăn lương doanh số (công)
            return $this->calculateCommissionBasedSalary($user, $shipments, $salaryPeriod);
        } else {
            // Tài xế ăn lương cơ bản
            return $this->calculateBasicSalary($user, $shipments, $salaryPeriod);
        }
    }

    /**
     * Calculate basic salary (tài xế ăn lương cơ bản)
     * 
     * @param User $user
     * @param Collection $shipments
     * @param mixed $salaryPeriod
     * @return array
     */
    protected function calculateBasicSalary(User $user, Collection $shipments, $salaryPeriod): array
    {
        $baseSalary = $user->salary_base ?? 0;
        $totalAllowance = 0;
        
        // Get salary advance requests
        $totalTypeSalary = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypeBonus = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypePenalty = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        
        // Process shipment deductions for allowances
        if ($user->role === 'driver') {
            // Role = driver: Tính phụ cấp từ chuyến hàng
            foreach ($shipments as $shipment) {
                $totalAllowance += $shipment->shipmentDeductionTypeDriverAndBusboy($user->id)->sum('amount') ?? 0;
            }
        } else {
            // Role khác: Phụ cấp = PHỤ CẤP CƠM NGÀY + tổng chi phí khác
            $lunchAllowance = 22 * 35000; // 22 ngày × 35,000 VND
            $otherCosts = $user->getSalaryAdvancesRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $salaryPeriod->start_date, $salaryPeriod->end_date)->sum('amount') ?? 0;
            $totalAllowance = $lunchAllowance + $otherCosts;
        }
        
        // Calculate total before insurance
        $totalBeforeInsurance = ($baseSalary + $totalAllowance + $totalTypeBonus) - ($totalTypeSalary + $totalTypePenalty);
        
        // Calculate social insurance based on settings
        $socialInsurance = $this->calculateSocialInsurance($totalBeforeInsurance, $user, $salaryPeriod->start_date, $salaryPeriod->end_date);
        
        // Calculate final salary
        $totalSalary = $totalBeforeInsurance;
        $netSalary = $totalBeforeInsurance - $socialInsurance;
        
        return [
            'baseSalary' => $baseSalary,
            'totalAllowance' => $totalAllowance,
            'totalBeforeInsurance' => $totalBeforeInsurance,
            'socialInsurance' => $socialInsurance,
            'totalSalary' => $totalSalary,
            'netSalary' => $netSalary,
            'totalTypeSalary' => $totalTypeSalary,
            'totalTypeBonus' => $totalTypeBonus,
            'totalTypePenalty' => $totalTypePenalty
        ];
    }

    /**
     * Calculate commission-based salary (tài xế ăn lương doanh số)
     * 
     * @param User $user
     * @param Collection $shipments
     * @param mixed $salaryPeriod
     * @return array
     */
    protected function calculateCommissionBasedSalary(User $user, Collection $shipments, $salaryPeriod): array
    {
        $totalAllowance = 0;
        $totalTripValue = 0;
        
        // Get salary advance requests
        $totalTypeSalary = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypeBonus = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $salaryPeriod->start_date, $salaryPeriod->end_date);
        $totalTypePenalty = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $salaryPeriod->start_date, $salaryPeriod->end_date);
        
        // Calculate total trip value and allowances
        foreach ($shipments as $shipment) {
            // Tính tổng giá trị chuyến xe: sum(unit_price_for_driver * trip_count) cho commission salary
            // Chỉ sử dụng unit_price_for_driver, nếu null hoặc 0 thì giữ 0
            $unitPrice = $shipment->unit_price_for_driver ?? 0;
            $tripCount = $shipment->trip_count ?? 1;
            $totalTripValue += ($unitPrice * $tripCount);
        }
        
        // Tính phụ cấp theo role
        if ($user->role === 'driver') {
            // Role = driver: Tính phụ cấp từ chuyến hàng
            foreach ($shipments as $shipment) {
                $totalAllowance += $shipment->shipmentDeductionTypeDriverAndBusboy($user->id)->sum('amount') ?? 0;
            }
        } else {
            // Role khác: Phụ cấp = PHỤ CẤP CƠM NGÀY + tổng chi phí khác
            $lunchAllowance = 22 * 35000; // 22 ngày × 35,000 VND
            $otherCosts = $user->getSalaryAdvanceRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $salaryPeriod->start_date, $salaryPeriod->end_date)->sum('amount') ?? 0;
            $totalAllowance = $lunchAllowance + $otherCosts;
        }
        
        // Lương cơ bản = X% của tổng giá trị chuyến xe (X từ user.salary_by_percent)
        $commissionRate = $user->getSalaryByPercent() / 100; // Convert percentage to decimal
        $baseSalary = $totalTripValue * $commissionRate;
        $totalCommission = $baseSalary; // Commission amount = base salary cho loại này
        
        // Calculate total before insurance
        $totalBeforeInsurance = ($baseSalary + $totalAllowance + $totalTypeBonus) - ($totalTypePenalty);
        
        // Calculate social insurance based on settings
        $socialInsurance = $this->calculateSocialInsurance($totalBeforeInsurance, $user, $salaryPeriod->start_date, $salaryPeriod->end_date);
        
        // Calculate final salary
        $totalSalary = $totalBeforeInsurance;
        $netSalary = $totalBeforeInsurance - ($socialInsurance + $totalTypeSalary);
        
        return [
            'baseSalary' => $baseSalary,
            'totalAllowance' => $totalAllowance,
            'totalCommission' => $totalCommission,
            'totalBeforeInsurance' => $totalBeforeInsurance,
            'socialInsurance' => $socialInsurance,
            'totalSalary' => $totalSalary,
            'netSalary' => $netSalary,
            'totalTypeSalary' => $totalTypeSalary,
            'totalTypeBonus' => $totalTypeBonus,
            'totalTypePenalty' => $totalTypePenalty
        ];
    }

    /**
     * Check if vehicle is eligible for commission
     * Phân biệt theo biển số xe: Container + Đầu kéo
     * 
     * @param mixed $vehicle
     * @return bool
     */
    protected function isCommissionEligibleVehicle($vehicle): bool
    {
        if (!$vehicle || !$vehicle->vehicleType) {
            return false;
        }
        
        $vehicleTypeName = strtolower($vehicle->vehicleType->name ?? '');
        
        // Check if vehicle type is Container or Đầu kéo
        return str_contains($vehicleTypeName, 'container') || 
               str_contains($vehicleTypeName, 'đầu kéo') ||
               str_contains($vehicleTypeName, 'dau keo');
    }

    /**
     * Calculate social insurance based on settings
     * BHXH: X% của Y
     * X: settings.social_insurance_contribution_rate ?? 10.5
     * Y: settings.social_insurance_contribution_amount ?? 5500000
     * 
     * @param float $amount (không sử dụng nữa, chỉ giữ để tương thích)
     * @param \App\Models\User|null $user
     * @param \Carbon\Carbon|null $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return float
     */
    protected function calculateSocialInsurance(float $amount, ?\App\Models\User $user = null, ?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): float
    {
        // Nếu có user và không đóng bảo hiểm, trả về 0
        if ($user && !$user->shouldPayInsuranceForPeriod($startDate ?? now()->startOfMonth(), $endDate ?? now()->endOfMonth())) {
            return 0;
        }
        
        // Lấy settings từ database thông qua SettingService và parse decimal
        $rate = parseDecimal($this->settingService->get('social_insurance_contribution_rate', 10.5));
        
        // Sử dụng mức lương đóng BHXH cá nhân nếu có user, ngược lại dùng setting
        $insuranceAmount = $user ? $user->getSocialInsuranceAmount() : 
                          parseDecimal($this->settingService->get('social_insurance_contribution_amount', 5500000));
        
        // Tính BHXH: X% của Y (không phụ thuộc vào $amount)
        return $insuranceAmount * ($rate / 100);
    }
}
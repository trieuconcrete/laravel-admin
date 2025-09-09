<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use App\Models\User;
use App\Models\SalaryAdvanceRequest;
use App\Repositories\Interface\UserRepositoryInterface as UserRepository;
use App\Repositories\Interface\DriverLicenseRepositoryInterface as DriverLicenseRepository;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Storage;
use App\Models\DriverLicense;
use App\Constants;
use App\Models\Position;
use App\Models\ShipmentDeductionType;
use App\Exports\SalaryExport;
use App\Exports\SalaryCommissionExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Repositories\Interface\PositionRepositoryInterface as PositionRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interface\ShipmentRepositoryInterface as ShipmentRepository;
use App\Repositories\Interface\SalaryAdvanceRequestRepositoryInterface as SalaryAdvanceRequestRepository;
use App\Enum\UserStatus as EnumUserStatus;
use App\Enum\SalaryType;
use Illuminate\Http\JsonResponse;
use App\Services\SalaryService;

class UserService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\UserRepositoryInterface $userRepository
     * @param \App\Repositories\Interface\DriverLicenseRepositoryInterface $driverLicenseRepository
     * @param \App\Repositories\Interface\PositionRepositoryInterface $positionRepository
     * @param \App\Repositories\Interface\ShipmentRepositoryInterface $shipmentRepository
     * @param \App\Repositories\Interface\SalaryAdvanceRequestRepositoryInterface $salaryAdvanceRequestRepository
     * @param \App\Services\SalaryService $salaryService
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected DriverLicenseRepository $driverLicenseRepository,
        protected PositionRepository $positionRepository,
        protected ShipmentRepository $shipmentRepository,
        protected SalaryAdvanceRequestRepository $salaryAdvanceRequestRepository,
        protected SalaryService $salaryService,
    ) {}

    /**
     * Store a newly created user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\User
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            $isDriver = (bool) $request->add_driver;

            $data = $request->all();

            // Format salary
            $data['salary_base'] = $request->salary_base ? str_replace(',', '', $request->salary_base) : 0;

            // Generate password
            $data['password'] = Hash::make($data['password'] ?? 'password');

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $data['avatar'] = ImageHelper::upload($request->file('avatar'));
            }

            // Handle checkbox fields - if not present in request, set to false
            if (!isset($data['has_insurance'])) {
                $data['has_insurance'] = false;
            } else {
                $data['has_insurance'] = (bool) $data['has_insurance'];
            }
            
            // Set role and position
            $data['role'] = $isDriver ? User::ROLE_DRIVER : ($request->is_admin ? User::ROLE_ADMIN : User::ROLE_STAFF);
            if ($isDriver) {
                $position = $this->positionRepository->findBy(['code' => Position::POSITION_TX]);
                $data['position_id'] = $position->id ?? null;
                
                // Set default salary type for drivers
                $data['salary_type'] = $request->salary_type ?? SalaryType::BASIC_SALARY->value;
            }

            // Create user
            $user = $this->userRepository->create($data);
            if (!$user) {
                throw new \Exception('Tạo người dùng thất bại');
            }

            // If is driver, create driver license
            if ($isDriver) {
                $this->driverLicenseRepository->create([
                    'user_id' => $user->id,
                    'license_type' => $request->license_type,
                    'expiry_date' => $request->license_expire_date,
                    'license_number' => null,
                    'issue_date' => Carbon::today(),
                    'issued_by' => null
                ]);
            }

            // Assign position
            $user->assignPosition((int) $user->position_id);

            // Sync salary for the new user for current month
            $currentMonth = now()->format('m/Y');
            $result = $this->salaryService->syncSalaryForUser($user, $currentMonth);
            
            // Log the result for debugging
            Log::info('Salary sync result for new user', [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'month' => $currentMonth,
                'result' => $result
            ]);

            return $user;
        } catch (\Throwable $e) {
            Log::error('Tạo người dùng thất bại', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \App\Models\User $user
     */
    public function update(Request $request, User $user): User
    {
        $data = $request->all();

        // handle salary
        if ($request->user_action == Constants::USER_ACTION_CHANGE_INFORMATION) {
            $data['salary_base'] = $request->salary_base ? str_replace(',', '', $request->salary_base) : 0;
        }

        // Handle password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Handle avatar upload
        if ($request->avatar) {
            $data['avatar'] = ImageHelper::replace($user->avatar, $data['avatar'], 'avatars');
        } else {
            unset($data['avatar']);
        }
        
        // Handle checkbox fields - if not present in request, set to false
        if (!isset($data['has_insurance'])) {
            $data['has_insurance'] = false;
        } else {
            $data['has_insurance'] = (bool) $data['has_insurance'];
        }
        
        if ($request->is_admin) {
            $data['role'] = User::ROLE_ADMIN;
        }

        // dd($data);
        // Update user
        $user = $this->userRepository->update($user->id, $data);

        // Handle Driver License
        if ($request->user_action == Constants::USER_ACTION_CHANGE_LICENSE) {
            $this->updateDriverLicense($user, $request);
        }

        // Sync salary if salary information was changed
        if ($request->user_action == Constants::USER_ACTION_CHANGE_INFORMATION) {
            $currentMonth = now()->format('m/Y');
            $result = $this->salaryService->syncSalaryForUser($user, $currentMonth);
            
            // Log the result for debugging
            Log::info('Salary sync result for updated user', [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'month' => $currentMonth,
                'result' => $result
            ]);
        }

        return $user;
    }

    /**
     * Summary of updateDriverLicense
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function updateDriverLicense(User $user, Request $request): void
    {
        $license = $user->license ?? new DriverLicense(['user_id' => $user->id]);
        $licenseData = $request->all();
        $licenseData['status'] = $request->license_status;

        if ($request->license_file) {
            if ($license->license_file && Storage::disk('public')->exists($license->license_file)) {
                Storage::disk('public')->delete($license->license_file);
            }
            $licenseData['license_file'] = ImageHelper::replace($license->license_file, $request->license_file, 'licenses');
        } else {
            unset($licenseData['license_file']);
        }

        $license->fill($licenseData)->save();
    }
    
    /**
     * Get user salary details for a specific month
     * 
     * @param User $user
     * @param string $selectedMonth Format: m/Y (e.g., 06/2025)
     * @return array
     */
    public function getSalaryDetails(User $user, string $selectedMonth): array
    {
        // Parse month and year from selectedMonth
        list($month, $year) = explode('/', $selectedMonth);
        // Sử dụng logic tính ngày mới từ SalaryService (issue #197)
        $periodDates = $this->salaryService->calculateSalaryPeriodDates((int)$month, (int)$year);
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];
        
        // Get completed shipments for the user for the selected month
        $shipments = $this->shipmentRepository->getUserShipmentsByDateRange($user, $startDate, $endDate, true);
        $shipmentsInMonth = $this->shipmentRepository->getUserShipmentsByDateRange($user, $startDate, $endDate);

        // Calculate salary details
        $salaryDetails = [];
        
        // Process shipment deductions for salary calculation - chỉ tính cho shipment đã hoàn thành
        foreach ($shipments as $shipment) {
            $shipmentAllowance = $shipment->shipmentDeductionTypeDriverAndBusboy($user->id)->sum('amount') ?? 0;
            
            // Không tính chi phí chuyến hàng vào lương nhân viên
            // $shipmentAmount = $shipment->shipmentDeductionTypeExpense()->sum('amount') ?? 0;
            
            // Add to salary details
            $salaryDetails[] = [
                'shipment_id' => $shipment->id,
                'shipment_code' => $shipment->shipment_code,
                'date' => $shipment->departure_time,
                'amount' => 0, // Không tính chi phí chuyến hàng
                'allowance' => $shipmentAllowance,
                'allowance_note' => $shipment->notes,
                'status' => $shipment->status_label,
                'notes' => $shipment->notes
            ];
        }

        $totalExpenses = 0; // Không tính chi phí chuyến hàng vào lương
        
        $totalOtherDeduction = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $startDate, $endDate);
        $totalBonus = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $startDate, $endDate);
        $totalPenalty = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $startDate, $endDate);
        $totalPaid = $user->getTotalSalaryPayments($startDate, $endDate);
        
        // Tính trợ cấp theo role
        $totalAllowance = 0;
        if ($user->role === 'driver') {
            // Role = driver: Giữ nguyên logic hiện tại (từ chuyến hàng)
            $totalAllowance = array_sum(array_column($salaryDetails, 'allowance')) ?? 0;
        } else {
            // Role khác: Trợ cấp = PHỤ CẤP CƠM NGÀY + tổng chi phí khác
            $lunchAllowance = 22 * 35000; // 22 ngày × 35,000 VND
            $otherCosts = $user->getSalaryAdvancesRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $startDate, $endDate)->sum('amount') ?? 0;
            $totalAllowance = $lunchAllowance + $otherCosts;
        }
        
        // Tính lương cơ bản theo loại lương
        $salaryType = $user->salary_type?->value ?? 1; // 1: BASIC_SALARY, 2: COMMISSION_SALARY
        $salaryBase = 0;
        $totalTripValue = 0;
        $commissionAmount = 0;
        
        if ($salaryType == 2) { // COMMISSION_SALARY - Lương doanh số
            // Tính tổng giá trị chuyến xe: sum(unit_price_for_driver * trip_count) cho commission salary
            foreach ($shipments as $shipment) {
                // Chỉ sử dụng unit_price_for_driver, nếu null hoặc 0 thì giữ 0
                $unitPrice = $shipment->unit_price_for_driver ?? 0;
                $tripCountPerShipment = $shipment->trip_count ?? 1;
                $totalTripValue += ($unitPrice * $tripCountPerShipment);
            }
            
            // Lương cơ bản = X% của tổng giá trị chuyến xe (X từ user.salary_by_percent)
            $commissionRate = $user->getSalaryByPercent() / 100; // Convert percentage to decimal
            $salaryBase = $totalTripValue * $commissionRate;
            $commissionAmount = $salaryBase;
        } else { // BASIC_SALARY - Lương cơ bản
            $salaryBase = $user->salary_base ?? 0;
        }
        
        // Calculate insurance deduction: X% của Y (từ settings)
        $totalBeforeInsurance = ($salaryBase + $totalAllowance + $totalBonus) - ( $totalPenalty);
        
        // Kiểm tra xem user có đóng bảo hiểm không
        $insuranceDeduction = 0;
        if ($user->shouldPayInsuranceForPeriod($startDate, $endDate)) {
            // Lấy settings từ database và parse decimal
            $insuranceRate = parseDecimal(\App\Models\Setting::get('social_insurance_contribution_rate', 10.5));
            $insuranceAmount = parseDecimal(\App\Models\Setting::get('social_insurance_contribution_amount', 5500000));
            
            // Tính BHXH: X% của Y (không phụ thuộc vào totalBeforeInsurance)
            $insuranceDeduction = $insuranceAmount * ($insuranceRate / 100);
        }
        
        // dd($totalBeforeInsurance, $insuranceDeduction, $salaryBase,$totalAllowance,$totalBonus, $totalOtherDeduction, $totalPenalty);
        // Calculate total salary - updated formula
        $totalSalary = $totalBeforeInsurance - ($insuranceDeduction + $totalOtherDeduction);
        
        return [
            'shipments' => $shipments,
            'shipmentsInMonth' => $shipmentsInMonth,
            'selectedMonth' => $selectedMonth,
            'salaryBase' => $salaryBase, // lương cơ bản (đã tính theo loại)
            'totalAllowance' => $totalAllowance, // tổng phụ cấp
            'totalExpenses' => $totalExpenses, // tổng chi phí (không tính)
            'insuranceDeduction' => $insuranceDeduction, // khấu trừ bảo hiểm
            'totalSalary' => $totalSalary, // tổng lương
            'salaryDetails' => $salaryDetails,
            'totalPaid' => $totalPaid, // số tiền đã thanh toán
            'salaryType' => $salaryType, // loại lương
            'totalTripValue' => $totalTripValue, // tổng giá trị chuyến xe
            'commissionAmount' => $commissionAmount, // số tiền hoa hồng
            'allowanceBreakdown' => $user->role === 'driver' ? null : [
                'lunchAllowance' => 22 * 35000,
                'otherCosts' => $user->getSalaryAdvancesRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $startDate, $endDate)->sum('amount') ?? 0,
                'otherCostsDetails' => $user->getSalaryAdvancesRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $startDate, $endDate)
            ]
        ];
    }
    
    /**
     * Get users with filters for listing
     *
     * @param array $filters
     * @param int|null $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsersWithFilters(array $filters = [], ?int $perPage = 10)
    {
        return $this->userRepository->getUsersWithFilters($filters, $perPage);
    }
    
    /**
     * Get data for user listing page
     *
     * @return array
     */
    public function getUserListingData(): array
    {
        return [
            'positions' => Position::active()->pluck('name', 'id'),
            'licenses' => DriverLicense::getCarLicenseTypes(),
            'statuses' => EnumUserStatus::options()
        ];
    }

    /**
     * Export user salary data to Excel
     *
     * @param User $user
     * @param string $selectedMonth Format: m/Y (e.g., 06/2025)
     * @return BinaryFileResponse
     */
    public function exportUserSalary(User $user, string $selectedMonth): BinaryFileResponse
    {
        // Parse month and year from selectedMonth
        list($month, $year) = explode('/', $selectedMonth);
        
        // Format month for query and filename
        $formattedMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        // Sử dụng logic tính ngày mới từ SalaryService (issue #197)
        $periodDates = $this->salaryService->calculateSalaryPeriodDates((int)$month, (int)$year);
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];
        
        // Get shipments for the user for the selected month using date range
        $shipments = $this->shipmentRepository->getUserShipmentsByDateRange($user, $startDate, $endDate);
            
        // Create filename - ensure safe characters
        $timestamp = Carbon::now()->format('Ymd_His');
        $safeEmployeeCode = str_replace(['/', '\\', ' '], '_', $user->employee_code ?? 'unknown');
        $fileName = 'bangluong_' . $safeEmployeeCode . '_' . $month . '_' . $year . '_' . $timestamp . '.xlsx';
        
        // Choose export class based on user's salary type
        if ($user->salary_type?->value == SalaryType::COMMISSION_SALARY->value) {
            // Tài xế lương doanh số: sử dụng SalaryCommissionExport
            return Excel::download(new SalaryCommissionExport($user, $shipments, $formattedMonth), $fileName);
        } else {
            // Tài xế lương cơ bản hoặc mặc định: sử dụng SalaryExport
            return Excel::download(new SalaryExport($user, $shipments, $formattedMonth), $fileName);
        }
    }
    
    /**
     * Create a new salary advance request
     *
     * @param array $data
     * @return SalaryAdvanceRequest
     */
    public function createSalaryAdvanceRequest(array $data): SalaryAdvanceRequest
    {
        // Format advance month if not provided
        if (!isset($data['advance_month'])) {
            $data['advance_month'] = Carbon::now()->format('Y-m-d');
        }
        
        // Format amount (remove commas)
        if (isset($data['amount'])) {
            $data['amount'] = str_replace(',', '', $data['amount']);
        }
        
        // Set request date if not provided
        if (!isset($data['request_date'])) {
            $data['request_date'] = Carbon::now();
        }
        
        // Set created_by if not provided
        if (!isset($data['created_by'])) {
            $data['created_by'] = Auth::id() ?? null;
        }
        
        $salaryAdvanceRequest = $this->salaryAdvanceRequestRepository->create($data);
        
        // Check if this is a payment request with approved/paid status
        if ($salaryAdvanceRequest->type === SalaryAdvanceRequest::TYPE_PAYMENT && 
            in_array($salaryAdvanceRequest->status, [SalaryAdvanceRequest::STATUS_APPROVED, SalaryAdvanceRequest::STATUS_PAID])) {
            $this->processSalaryPayment($salaryAdvanceRequest);
        }
        
        return $salaryAdvanceRequest;
    }
    
    /**
     * Get salary advance requests by user
     *
     * @param User $user
     * @param string|null $month Format: m/Y
     * @return array
     */
    public function getSalaryAdvanceRequests(User $user, ?string $month = null): array
    {
        if ($month) {
            list($m, $y) = explode('/', $month);
            $formattedMonth = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $requests = $this->salaryAdvanceRequestRepository->getByUserAndMonth($user, $formattedMonth);
        } else {
            $requests = $this->salaryAdvanceRequestRepository->getByUser($user);
        }

        $formattedRequests = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_code' => $request->request_code,
                'amount' => $request->amount,
                'formatted_amount' => number_format($request->amount, 0, ',', '.'),
                'status' => $request->status,
                'status_label' => $request->status_label,
                'status_color' => $request->status_color,
                'reason' => $request->reason,
                'advance_month' => $request->advance_month ? $request->advance_month->format(config('app.date_format', 'd/m/Y')) : null,
                'request_date' => $request->request_date,
                'formatted_request_date' => $request->request_date ? $request->request_date->format(config('app.date_format', 'd/m/Y')) : null,
                'created_at' => $request->created_at,
                'formatted_created_at' => $request->created_at ? $request->created_at->format(config('app.date_format', 'd/m/Y')) : null,
                'type_color' => $request->type_color,
                'type_label' => $request->type_label,
                'type' => $request->type
            ];
        });
        
        return [
            'requests' => $formattedRequests,
            'statuses' => SalaryAdvanceRequest::getStatuses()
        ];
    }
    
    /**
     * Process salary payment for payment type requests
     *
     * @param SalaryAdvanceRequest $salaryAdvanceRequest
     * @return void
     */
    private function processSalaryPayment(SalaryAdvanceRequest $salaryAdvanceRequest): void
    {
        try {
            // Get the user
            $user = $salaryAdvanceRequest->user;
            if (!$user) {
                Log::error('Không tìm thấy người dùng cho yêu cầu thanh toán lương', ['request_id' => $salaryAdvanceRequest->id]);
                return;
            }
            
            // Parse the advance month to get period
            $advanceMonth = Carbon::parse($salaryAdvanceRequest->advance_month);
            $monthFormat = $advanceMonth->format('m/Y');
            
            // Sync salary data for user first to ensure SalaryDetail exists
            $syncResult = $this->salaryService->syncSalaryForUser($user, $monthFormat);
            
            if (!$syncResult['success']) {
                Log::error('Đồng bộ dữ liệu lương cho yêu cầu thanh toán thất bại', [
                    'request_id' => $salaryAdvanceRequest->id,
                    'user_id' => $user->id,
                    'month' => $monthFormat,
                    'error' => $syncResult['message']
                ]);
                return;
            }
            
            // Get salary detail ID from sync result
            $salaryDetailId = $syncResult['salary_detail_id'];
            
            if (!$salaryDetailId) {
                Log::error('Không tìm thấy ID chi tiết lương trong kết quả đồng bộ', [
                    'request_id' => $salaryAdvanceRequest->id,
                    'user_id' => $user->id,
                    'month' => $monthFormat,
                    'sync_result' => $syncResult
                ]);
                return;
            }
            
            // Find salary detail to check if already paid
            $salaryDetail = \App\Models\SalaryDetail::find($salaryDetailId);
            
            if (!$salaryDetail) {
                Log::error('Salary detail not found after sync', [
                    'request_id' => $salaryAdvanceRequest->id,
                    'salary_detail_id' => $salaryDetailId,
                    'user_id' => $user->id
                ]);
                return;
            }
            
            // Check if this is a repeated payment for logging
            $isRepeatedPayment = $salaryDetail->status === 'paid';
            
            // Call the salary controller's processPayment method (allow multiple payments)
            $salaryController = app(\App\Http\Controllers\Admin\SalaryController::class);
            $response = $salaryController->processPayment($salaryDetail->salary_id);
            
            // Log the result
            if ($response->getStatusCode() === 200) {
                Log::info('Salary payment processed successfully via payment request', [
                    'request_id' => $salaryAdvanceRequest->id,
                    'salary_id' => $salaryDetail->salary_id,
                    'user_id' => $user->id,
                    'is_repeated_payment' => $isRepeatedPayment
                ]);
            } else {
                Log::error('Failed to process salary payment via payment request', [
                    'request_id' => $salaryAdvanceRequest->id,
                    'salary_id' => $salaryDetail->salary_id,
                    'response' => $response->getContent()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error processing salary payment via payment request', [
                'request_id' => $salaryAdvanceRequest->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Process salary payment for payment type requests (public method for controller)
     *
     * @param SalaryAdvanceRequest $salaryAdvanceRequest
     * @return void
     */
    public function processSalaryPaymentForRequest(SalaryAdvanceRequest $salaryAdvanceRequest): void
    {
        $this->processSalaryPayment($salaryAdvanceRequest);
    }

    /**
     * Get salary payment status for a user in a specific month
     *
     * @param User $user
     * @param string $month Format: m/Y (e.g., 07/2025)
     * @return array
     */
    public function getSalaryPaymentStatus(User $user, string $month): array
    {
        $paymentStatus = $user->isSalaryFullyPaid($month);
        
        // Sử dụng logic tính ngày mới từ SalaryService (issue #197)
        list($monthNum, $year) = explode('/', $month);
        $periodDates = $this->salaryService->calculateSalaryPeriodDates((int)$monthNum, (int)$year);
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];
        
        $paymentRequests = $user->salaryAdvanceRequests()
            ->where('type', SalaryAdvanceRequest::TYPE_PAYMENT)
            ->whereBetween('advance_month', [$startDate, $endDate])
            ->whereIn('status', ['approved', SalaryAdvanceRequest::STATUS_PAID])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $paymentHistory = $paymentRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'request_code' => $request->request_code,
                'amount' => $request->amount,
                'formatted_amount' => number_format($request->amount, 0, ',', '.'),
                'status' => $request->status,
                'status_label' => $request->status_label,
                'request_date' => $request->request_date,
                'formatted_request_date' => $request->request_date ? $request->request_date->format('d/m/Y H:i') : null,
                'reason' => $request->reason
            ];
        });
        
        return [
            'payment_status' => $paymentStatus,
            'payment_history' => $paymentHistory,
            'total_payments' => $paymentRequests->count(),
            'month' => $month
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use App\Models\User;
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
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Repositories\Interface\PositionRepositoryInterface as PositionRepository;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\ShipmentRepositoryInterface as ShipmentRepository;
use App\Enum\UserStatus as EnumUserStatus;

class UserService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\UserRepositoryInterface $userRepository
     * @param \App\Repositories\Interface\DriverLicenseRepositoryInterface $driverLicenseRepository
     * @param \App\Repositories\Interface\PositionRepositoryInterface $positionRepository
     * @param \App\Repositories\Interface\ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected DriverLicenseRepository $driverLicenseRepository,
        protected PositionRepository $positionRepository,
        protected ShipmentRepository $shipmentRepository,
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

            // Set role and position
            $data['role'] = $isDriver ? User::ROLE_DRIVER : User::ROLE_STAFF;
            if ($isDriver) {
                $position = $this->positionRepository->findBy(['code' => Position::POSITION_TX]);
                $data['position_id'] = $position->id ?? null;
            }

            // Create user
            $user = $this->userRepository->create($data);
            if (!$user) {
                throw new \Exception('Create user failed');
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

            return $user;
        } catch (\Throwable $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
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

        // Update user
        $user = $this->userRepository->update($user->id, $data);

        // Handle Driver License
        if ($request->user_action == Constants::USER_ACTION_CHANGE_LICENSE) {
            $this->updateDriverLicense($user, $request);
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
        
        // Get shipments for the user for the selected month
        $shipments = $this->shipmentRepository->getUserShipments($user, $month, $year);
        
        // Calculate salary details
        $salaryDetails = [];
        
        // Process shipment deductions for salary calculation
        foreach ($shipments as $shipment) {
            $shipmentAllowance = $shipment->shipmentDeductionTypeDriverAndBusboy($user->id)->sum('amount') ?? 0;
            $shipmentAmount = $shipment->shipmentDeductionTypeExpense()->sum('amount') ?? 0;
            
            // Add to salary details if there's an amount or allowance
            if ($shipmentAmount > 0 || $shipmentAllowance > 0) {
                $salaryDetails[] = [
                    'shipment_id' => $shipment->id,
                    'shipment_code' => $shipment->shipment_code,
                    'date' => $shipment->departure_time,
                    'amount' => $shipmentAmount,
                    'allowance' => $shipmentAllowance,
                    'allowance_note' => $shipment->notes,
                    'status' => $shipment->status_label,
                    'notes' => $shipment->notes
                ];
            }
        }

        $totalAllowance = array_sum(array_column($salaryDetails, 'allowance')) ?? 0;
        $totalExpenses = array_sum(array_column($salaryDetails, 'amount')) ?? 0;
        
        // Calculate insurance deduction (10% of total: salary base + allowances + expenses)
        $totalBeforeInsurance = ($user->salary_base ?? 0) + $totalAllowance + $totalExpenses;
        $insuranceDeduction = $totalBeforeInsurance * 0.1; // 10% of total
        
        // Calculate total salary - updated formula
        $totalSalary = $totalBeforeInsurance - $insuranceDeduction;
        
        return [
            'shipments' => $shipments,
            'selectedMonth' => $selectedMonth,
            'salaryBase' => $user->salary_base ?? 0,
            'totalAllowance' => $totalAllowance,
            'totalExpenses' => $totalExpenses,
            'insuranceDeduction' => $insuranceDeduction,
            'totalSalary' => $totalSalary,
            'salaryDetails' => $salaryDetails
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
            'positions' => Position::pluck('name', 'id'),
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
        
        // Get start and end date of the month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Get shipments for the user for the selected month using date range
        $shipments = $this->shipmentRepository->getUserShipmentsByDateRange($user, $startDate, $endDate);
        
        // Create filename
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = 'bangluong_' . $user->employee_code . '_' . $month . '_' . $year . '_' . $timestamp . '.xlsx';
        
        // Generate and download the Excel file
        return Excel::download(new SalaryExport($user, $shipments, $formattedMonth), $fileName);
    }
}

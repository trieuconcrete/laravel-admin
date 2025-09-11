<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\ShipmentDeductionType;
use App\Models\SalaryAdvanceRequest;
use App\Exports\UserExport;
use App\Exports\SalaryExport;
use App\Exports\OfficeSalaryExport;
use Illuminate\Http\Request;
use App\Models\DriverLicense;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\SalaryAdvanceRequest\StoreSalaryAdvanceRequestRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Enum\UserStatus as EnumUserStatus;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Traits\UsesSystemDateFormat;
use App\Http\Requests\SalaryAdvanceRequest\UpdateSalaryAdvanceRequestRequest;
use App\Services\SalaryService;

/**
 * Summary of UserController
 */
class UserController extends Controller
{
    use AuthorizesRequests;
    use UsesSystemDateFormat;

    protected $userService;
    protected $salaryService;

    /**
     * Summary of __construct
     * @param \App\Services\UserService $userService
     * @param \App\Services\SalaryService $salaryService
     */
    public function __construct(UserService $userService, SalaryService $salaryService) {
        $this->userService = $userService;
        $this->salaryService = $salaryService;
    }

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Prepare filters from request
        $filters = [
            'search' => $request->search,
            'position_id' => $request->position_id,
            'status' => $request->status,
            'exclude_current_user' => Auth::id()
        ];
        
        // Get users with filters
        $users = $this->userService->getUsersWithFilters($filters, 10);
        
        // Get data for listing page
        $listingData = $this->userService->getUserListingData();
        
        return view('admin.users.index', [
            'users' => $users,
            'positions' => $listingData['positions'],
            'licenses' => $listingData['licenses'],
            'statuses' => $listingData['statuses'],
            'licenseStatuses' => $listingData['licenseStatuses']
        ]);
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\User\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->userService->store($request);

            DB::commit();

            return response()->json(['message' => 'Tạo người dùng thành công.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tạo người dùng thất bại', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Summary of show
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function show(User $user, Request $request)
    {
        $this->authorize('view', $user);
        
        // Get reference data
        $positions = Position::pluck('name', 'id');
        $licenses = DriverLicense::getCarLicenseTypes();
        $licenseStatuses = DriverLicense::getStatuses();
        
        // Get selected month or default to current month
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        
        // Get salary details from service
        $salaryData = $this->userService->getSalaryDetails($user, $selectedMonth);
         
        // Get salary advance requests for the user
        $salaryAdvanceData = $this->userService->getSalaryAdvanceRequests($user, $selectedMonth);

        $parsedMonth = Carbon::createFromFormat('m/Y', $selectedMonth);
        $periodDates = $this->salaryService->calculateSalaryPeriodDates($parsedMonth->format('m'), $parsedMonth->format('Y'));
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];

        $totalOtherDeduction = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $startDate, $endDate);
        $totalBonus = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $startDate, $endDate);
        $totalPenalty = $user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $startDate, $endDate);
        $totalPaid = $user->getTotalSalaryPayments($startDate, $endDate);
        
        // Get salary payment status
        $paymentStatusData = $this->userService->getSalaryPaymentStatus($user, $selectedMonth);
        
        
        // Extract data from service responses
        extract($salaryData);
        extract($salaryAdvanceData);
        $statuses = EnumUserStatus::options();
        
        // Handle AJAX request for salary data refresh
        if ($request->ajax() && $request->get('ajax') === 'true') {
            // Ensure all values are properly formatted as numbers
            $chartSeries = [
                (float) $salaryBase,
                (float) $totalAllowance,
                (float) $insuranceDeduction,
                (float) $totalPenalty,
                (float) $totalOtherDeduction,
                (float) $totalBonus
            ];
            
            return response()->json([
                'success' => true,
                'salaryData' => view('admin.users.partials.salary-table', compact(
                    'salaryBase', 'totalAllowance', 'totalBonus', 'totalPenalty', 
                    'totalOtherDeduction', 'insuranceDeduction', 'totalSalary', 'totalPaid', 'user'
                ))->render(),
                'chartData' => [
                    'series' => $chartSeries,
                    'labels' => ['Lương cơ bản', 'Trợ cấp', 'BHXH', 'Phạt', 'Ứng lương', 'Thưởng']
                ],
                'summaryData' => [
                    'salaryBase' => (float) $salaryBase,
                    'totalAllowance' => (float) $totalAllowance,
                    'totalBonus' => (float) $totalBonus,
                    'totalPenalty' => (float) $totalPenalty,
                    'totalOtherDeduction' => (float) $totalOtherDeduction,
                    'insuranceDeduction' => (float) $insuranceDeduction,
                    'totalSalary' => (float) $totalSalary,
                    'totalPaid' => (float) $totalPaid
                ],
                'paymentStatus' => $paymentStatusData
            ]);
        }
        
        return view('admin.users.show', compact(
            'user', 'positions', 'licenses', 'statuses', 'licenseStatuses',
            'shipments', 'shipmentsInMonth', 'selectedMonth', 'salaryBase', 'totalAllowance', 
            'insuranceDeduction', 'totalSalary', 'salaryDetails',
            'requests', 'totalOtherDeduction', 'totalBonus', 'totalPenalty', 'totalPaid',
            'paymentStatusData', 'salaryType', 'totalTripValue', 'commissionAmount'
        ));
    }

    /**
     * Summary of edit
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        return redirect()->back()->with('error', 'Trang không hợp lệ.');
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $this->authorize('update', $user);
            $this->userService->update($request, $user);

            DB::commit();
            
            return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cập nhật người dùng thất bại', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('active_tab', $request->input('tab'));
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return back()->with('success', 'Xóa người dùng thành công.');
    }

    /**
     * Summary of export
     */
    public function export()
    {
        $users = User::all();
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = 'users_' . $timestamp . '.xlsx';

        return Excel::download(new UserExport($users), $fileName);
    }
    
    /**
     * Xuất bảng lương của người dùng theo tháng
     * 
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportSalary(User $user, Request $request)
    {
        $this->authorize('view', $user);
        
        // Lấy tháng được chọn hoặc mặc định là tháng hiện tại
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        
        // Use service to handle export logic
        return $this->userService->exportUserSalary($user, $selectedMonth);
    }

    /**
     * Xuất bảng lương văn phòng cho nhân viên ăn lương cơ bản (trừ tài xế)
     * 
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportOfficeSalary(User $user, Request $request)
    {
        $this->authorize('view', $user);
        
        // Kiểm tra điều kiện: nhân viên ăn lương cơ bản và không phải tài xế
        if (!$user->isEligibleForLunchAllowance()) {
            abort(403, 'Chỉ nhân viên văn phòng mới có thể xuất bảng lương này.');
        }
        
        $month = $request->get('month', now()->format('m/Y'));
        $timestamp = Carbon::now()->format('Ymd_His');
        // Thay thế "/" bằng "_" trong tên file để tránh lỗi
        $safeMonth = str_replace('/', '_', $month);
        $safeName = str_replace(['/', '\\', ' '], '_', $user->full_name);
        $fileName = 'bang_luong_van_phong_' . $safeName . '_' . $safeMonth . '_' . $timestamp . '.xlsx';

        return Excel::download(new OfficeSalaryExport($user, $month), $fileName);
    }
    
    /**
     * Create a new salary advance request for a user
     * 
     * @param User $user
     * @param StoreSalaryAdvanceRequestRequest $request
     * @return JsonResponse
     */
    public function createSalaryAdvanceRequest(User $user, StoreSalaryAdvanceRequestRequest $request): JsonResponse
    {
        // Kiểm tra quyền tạo SalaryAdvanceRequest
        $this->authorize('create', SalaryAdvanceRequest::class);
        
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = $user->id;
            
            // Use service to create salary advance request
            $salaryAdvanceRequest = $this->userService->createSalaryAdvanceRequest($data);
            
            /** process sync salary */
            $monthRequest = Carbon::parse($request->advance_month)->format('m/Y');
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
                'message' => 'Tạo yêu cầu thành công',
                'data' => [
                    'id' => $salaryAdvanceRequest->id,
                    'request_code' => $salaryAdvanceRequest->request_code,
                    'amount' => number_format($salaryAdvanceRequest->amount),
                    'status' => $salaryAdvanceRequest->status_label,
                    'status_color' => $salaryAdvanceRequest->status_color,
                    'created_at' => $salaryAdvanceRequest->created_at->format('d/m/Y H:i')
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tạo yêu cầu ứng lương thất bại', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get salary advance requests for a user
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function getSalaryAdvanceRequests(User $user, Request $request)
    {
        $this->authorize('view', $user);
        
        // Get selected month or default to current month
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        
        // Get salary advance requests
        $data = $this->userService->getSalaryAdvanceRequests($user, $selectedMonth);
        
        return response()->json($data);
    }

    /**
     * Summary of updateSalaryAdvanceRequest
     * @param \Illuminate\Http\Request $request
     * @param mixed $requestId
     * @return JsonResponse|mixed
     */
    public function updateSalaryAdvanceRequest(UpdateSalaryAdvanceRequestRequest $request, User $user, $requestId)
    {
        try {
            // Get salary advance request
            $salaryAdvanceRequest = SalaryAdvanceRequest::where('id', $requestId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Kiểm tra quyền update SalaryAdvanceRequest
            $this->authorize('update', $salaryAdvanceRequest);

            // Store old status for comparison
            $oldStatus = $salaryAdvanceRequest->status;
            $oldType = $salaryAdvanceRequest->type;

            // Update salary advance request
            $salaryAdvanceRequest->update($request->validated());

            // Check if this is a payment request with approved/paid status
            if ($salaryAdvanceRequest->type === SalaryAdvanceRequest::TYPE_PAYMENT && 
                in_array($salaryAdvanceRequest->status, [SalaryAdvanceRequest::STATUS_APPROVED, SalaryAdvanceRequest::STATUS_PAID]) &&
                ($oldStatus !== $salaryAdvanceRequest->status || $oldType !== $salaryAdvanceRequest->type)) {
                
                // Process salary payment
                $this->userService->processSalaryPaymentForRequest($salaryAdvanceRequest);
            }

            /** process sync salary */
            $monthRequest = Carbon::parse($request->advance_month)->format('m/Y');
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

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật yêu cầu thành công'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

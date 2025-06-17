<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\ShipmentDeductionType;
use App\Models\SalaryAdvanceRequest;
use App\Exports\UserExport;
use App\Exports\SalaryExport;
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

/**
 * Summary of UserController
 */
class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Summary of __construct
     * @param \App\Services\UserService $userService
     */
    public function __construct(protected UserService $userService) {}

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
            'statuses' => $listingData['statuses']
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

            return response()->json(['message' => 'User created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
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
        
        // Extract data from service responses
        extract($salaryData);
        extract($salaryAdvanceData);
        $statuses = EnumUserStatus::options();
        
        return view('admin.users.show', compact(
            'user', 'positions', 'licenses', 'statuses', 'licenseStatuses',
            'shipments', 'selectedMonth', 'salaryBase', 'totalAllowance', 
            'totalExpenses', 'insuranceDeduction', 'totalSalary', 'salaryDetails',
            'requests'
        ));
    }

    /**
     * Summary of edit
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
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
            
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('User update failed', ['error' => $e->getMessage()]);
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
        return back()->with('success', 'User deleted successfully.');
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
     * Create a new salary advance request for a user
     * 
     * @param User $user
     * @param StoreSalaryAdvanceRequestRequest $request
     * @return JsonResponse
     */
    public function createSalaryAdvanceRequest(User $user, StoreSalaryAdvanceRequestRequest $request): JsonResponse
    {
        $this->authorize('view', $user);
        
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['user_id'] = $user->id;
            
            // Use service to create salary advance request
            $salaryAdvanceRequest = $this->userService->createSalaryAdvanceRequest($data);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo yêu cầu ứng lương thành công',
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
            Log::error('Create salary advance request failed', ['error' => $e->getMessage()]);
            
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
}

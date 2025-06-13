<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Position;
use App\Models\Shipment;
use App\Models\ShipmentDeductionType;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Enum\UserStatus as EnumUserStatus;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('employee_code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->where('id', '!=', auth()->user()->id)
            ->whereNull('deleted_at')
            ->latest()
            ->paginate(10);
        $positions = Position::pluck('name', 'id');
        $licenses = DriverLicense::getCarLicenseTypes();
        $statuses = EnumUserStatus::options();
        
        return view('admin.users.index', compact([
            'users',
            'positions', 
            'licenses',
            'statuses'
        ]));
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
        $positions = Position::pluck('name', 'id');
        $licenses = DriverLicense::getCarLicenseTypes();
        $statuses = EnumUserStatus::options();
        $licenseStatuses = DriverLicense::getStatuses();
        
        // Get selected month or default to current month
        $selectedMonth = $request->get('month', now()->format('m/Y'));
        list($month, $year) = explode('/', $selectedMonth);
        
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
        $salaryBase = $user->salary_base ?? 0;
        $totalAllowance = 0;
        $totalExpenses = 0;
        $insuranceDeduction = 0;
        $salaryDetails = [];
        
        // Process shipment deductions for salary calculation
        foreach ($shipments as $shipment) {
            $shipmentAllowance = 0;
            $shipmentAmount = 0;
            $notes = '';
            foreach ($shipment->shipmentDeductions as $deduction) {
                if ($deduction->user_id == $user->id) {
                    // Check if this is an allowance (driver_and_busboy) or expense
                    if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_DRIVER_AND_BUSBOY) {
                        $shipmentAllowance += $deduction->amount;
                        $totalAllowance += $deduction->amount;
                        $notes = $deduction->notes;
                    } else if ($deduction->shipmentDeductionType && $deduction->shipmentDeductionType->type == ShipmentDeductionType::TYPE_EXPENSE) {
                        $shipmentAmount += $deduction->amount;
                        $totalExpenses += $deduction->amount; // Add to total expenses
                    }
                }
            }
            
            // Add to salary details if there's an amount or allowance
            if ($shipmentAmount > 0 || $shipmentAllowance > 0) {
                $salaryDetails[] = [
                    'shipment_id' => $shipment->id,
                    'shipment_code' => $shipment->shipment_code,
                    'date' => $shipment->departure_time,
                    'amount' => $shipmentAmount,
                    'allowance' => $shipmentAllowance,
                    'allowance_note' => $notes,
                    'status' => $shipment->status_label,
                    'notes' => $shipment->notes
                ];
            }
        }
        
        // Calculate insurance deduction (10% of total: salary base + allowances + expenses)
        $totalBeforeInsurance = $salaryBase + $totalAllowance + $totalExpenses;
        $insuranceDeduction = $totalBeforeInsurance * 0.1; // 10% of total
        
        // Calculate total salary - updated formula
        $totalSalary = $totalBeforeInsurance - $insuranceDeduction;
        
        return view('admin.users.show', compact(
            'user', 'positions', 'licenses', 'statuses', 'licenseStatuses',
            'shipments', 'selectedMonth', 'salaryBase', 'totalAllowance', 
            'totalExpenses', 'insuranceDeduction', 'totalSalary', 'salaryDetails'
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
        list($month, $year) = explode('/', $selectedMonth);
        
        // Format month for query
        $formattedMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        // Get shipments for the user for the selected month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $shipments = Shipment::whereHas('shipmentDeductions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereBetween('departure_time', [$startDate, $endDate])
            ->with(['shipmentDeductions', 'shipmentDeductions.shipmentDeductionType'])
            ->orderBy('departure_time')
            ->get();
        
        // Tạo tên file
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = 'bangluong_' . $user->employee_code . '_' . $month . '_' . $year . '_' . $timestamp . '.xlsx';
        
        return Excel::download(new SalaryExport($user, $shipments, $formattedMonth), $fileName);
    }
}

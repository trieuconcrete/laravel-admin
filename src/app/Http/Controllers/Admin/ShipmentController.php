<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\ShipmentRequest;
use App\Services\ShipmentService;
use App\Models\Shipment;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\ShipmentDeductionType;
use App\Enum\UserStatus;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    protected $shipmentService;

    /**
     * Summary of __construct
     * @param \App\Services\ShipmentService $shipmentService
     */
    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->input('status'),
            'departure_time' => $request->input('departure_time'),
            'estimated_arrival_time' => $request->input('estimated_arrival_time'),
            'keyword' => $request->input('keyword'),
        ];
        // Use getList instead of list to avoid PHP reserved keyword conflict
        $shipments = $this->shipmentService->getList($filters, 20);
        $shipmentStatus = Shipment::$statuses;

        return view('admin.shipments.index', compact('shipments', 'shipmentStatus'));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        $vehicles = Vehicle::where('status', Vehicle::STATUS_ACTIVE)->pluck('plate_number', 'vehicle_id');
        
        // Get drivers (tài xế)
        $users = User::whereIn('role', ['driver', 'assistant', 'helper'])
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->where('code', Position::POSITION_TX);
            })
            ->pluck('full_name', 'id')
            ->toArray();
            
        $deductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_EXPENSE)
            ->where('status', 'active')
            ->get();
            
        $personDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_DRIVER)
            ->where('status', 'active')
            ->get();
            
        $subPersonDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_BUS_DRIVER)
            ->where('status', 'active')
            ->get();
            
        $userPXs = User::whereIn('role', ['driver', 'assistant', 'helper', 'staff'])
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->whereIn('code', [Position::POSITION_PX, Position::POSITION_TX]);
            })
            ->pluck('full_name', 'id')
            ->toArray();
            
        // Debug log to check users
        if (app()->environment('local')) {
            logger('Users loaded in create method:', ['count' => count($users), 'users' => $users]);
        }
            
        return view('admin.shipments.create', compact(
            'customers', 
            'vehicles', 
            'users', 
            'deductionTypes', 
            'personDeductionTypes', 
            'subPersonDeductionTypes', 
            'userPXs'
        ));
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Shipment\ShipmentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ShipmentRequest $request)
    {
        try {
            $this->shipmentService->createShipment($request->validated());
            return redirect()->route('admin.shipments.index')->with('success', 'Tạo chuyến hàng thành công.');
        } catch (\Exception $e) {
            Log::error('Tạo chuyến hàng thất bại: '. $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Summary of edit
     * @param \App\Models\Shipment $shipment
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load(['goods', 'shipmentDeductions']);
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        $vehicles = Vehicle::where('status', Vehicle::STATUS_ACTIVE)->pluck('plate_number', 'vehicle_id');
        $users = User::whereIn('role', ['driver', 'assistant', 'helper'])
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->where('code', Position::POSITION_TX);
            })
            ->pluck('full_name', 'id');
        $deductionTypes = ShipmentDeductionType::where('type', 'expense')->where('status', 'active')->get();
        $personDeductionTypes =ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_DRIVER)
            ->where('status', 'active')
            ->get();
        
        $subPersonDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_BUS_DRIVER)
            ->where('status', 'active')
            ->get();

        $userPXs = User::whereIn('role', ['driver', 'assistant', 'helper', 'staff'])
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->whereIn('code', [Position::POSITION_PX, Position::POSITION_TX]);
            })
            ->pluck('full_name', 'id')
            ->toArray();
            
        $shipmentStatus = Shipment::$statuses;
        
        // Chuẩn bị dữ liệu cho form edit
        $shipmentDeductions = $shipment->shipmentDeductions()->whereNull('user_id')->get()->keyBy('shipment_deduction_type_id');
        $driverDeductions = $shipment->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function ($query) {
                $query->where('type', ShipmentDeductionType::TYPE_DRIVER);
            })
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id');
        
        $driverPXDeductions = $shipment->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function ($query) {
                $query->where('type', ShipmentDeductionType::TYPE_BUS_DRIVER);
            })
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id');

        // dd($driverDeductions);


        return view('admin.shipments.edit', compact(
            'shipment', 'customers', 'vehicles', 'users', 
            'deductionTypes', 'personDeductionTypes', 
            'subPersonDeductionTypes', 'shipmentDeductions', 'driverDeductions', 'shipmentStatus', 'userPXs', 'driverPXDeductions'
        ));
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\Shipment\ShipmentRequest $request
     * @param \App\Models\Shipment $shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ShipmentRequest $request, Shipment $shipment)
    {
        try {
            $this->shipmentService->update($shipment, $request->validated());
            return redirect()->route('admin.shipments.index')->with('success', 'Cập nhật chuyến hàng thành công.');
        } catch (\Exception $e) {
            Log::error('Cập nhật chuyến hàng thất bại: '. $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\Shipment $shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Shipment $shipment)
    {
        $this->shipmentService->delete($shipment);
        return back()->with('success', 'Xóa chuyến hàng thành công.');
    }
}

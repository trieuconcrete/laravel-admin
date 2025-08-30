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
use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;

class ShipmentController extends Controller
{
    protected $shipmentService;
    protected $vehicleRepository;

    /**
     * Summary of __construct
     * @param \App\Services\ShipmentService $shipmentService
     */
    public function __construct(ShipmentService $shipmentService, VehicleRepository $vehicleRepository)
    {
        $this->shipmentService = $shipmentService;
        $this->vehicleRepository = $vehicleRepository;
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
            'shipment_type' => $request->input('shipment_type'),
            'departure_time' => $request->input('departure_time'),
            'estimated_arrival_time' => $request->input('estimated_arrival_time'),
            'keyword' => $request->input('keyword'),
            'customer_id' => $request->input('customer_id'),
        ];
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        // Use getList instead of list to avoid PHP reserved keyword conflict
        $shipments = $this->shipmentService->getList($filters, perPage: 15);
        $shipmentStatus = Shipment::$statuses;
        $shipmentTypes = Shipment::$shipmentTypes;

        return view('admin.shipments.index', compact('shipments', 'customers', 'shipmentStatus', 'shipmentTypes'));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        $vehicles = $this->vehicleRepository->getVehiclesByIsCarRental(false);
        
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
            ->orderBy('order', 'asc')
            ->get();

        $carRentalDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_CAR_RENTAL_EXPENSE)
            ->where('status', 'active')
            ->orderBy('order', 'asc')
            ->get();
            
        $personDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_DRIVER)
            ->where('status', 'active')
            ->orderBy('order', 'asc')
            ->get();
            
        $subPersonDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_BUS_DRIVER)
            ->where('status', 'active')
            ->orderBy('order', 'asc')
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
            'carRentalDeductionTypes',
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
        $shipment->load(['vehicle', 'goods', 'shipmentDeductions']);
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        // Load tất cả vehicles, không filter theo carRental để có thể chọn bất kỳ xe nào
        $vehicles = $this->vehicleRepository->getVehiclesByIsCarRental(optional($shipment->vehicle)->is_car_rental);
        $users = User::whereIn('role', ['driver', 'assistant', 'helper'])
            ->where('status', UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->where('code', Position::POSITION_TX);
            })
            ->pluck('full_name', 'id')
            ->toArray(); // Chuyển Collection thành array
            
        // Debug log để kiểm tra
        if (app()->environment('local')) {
            logger('Users in edit method:', [
                'type' => gettype($users),
                'is_array' => is_array($users),
                'count' => count($users),
                'data' => $users
            ]);
            
            logger('Vehicles in edit method:', [
                'count' => $vehicles->count(),
                'vehicle_ids' => $vehicles->pluck('vehicle_id')->toArray(),
                'shipment_vehicle_id' => $shipment->vehicle_id,
                'shipment_is_car_rental' => $shipment->is_car_rental
            ]);
        }
        $deductionTypes = ShipmentDeductionType::where('type', 'expense')->where('status', 'active')->get();
        $carRentalDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_CAR_RENTAL_EXPENSE)
            ->where('status', 'active')
            ->orderBy('order', 'asc')
            ->get();
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
        $shipmentTypes = Shipment::$shipmentTypes;
        
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

        return view('admin.shipments.edit', compact(
            'shipment', 'customers', 'vehicles', 'users', 
            'deductionTypes', 'carRentalDeductionTypes', 'personDeductionTypes', 
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
            // Debug: Log received data
            if (app()->environment('local')) {
                Log::info('Shipment update - Raw request data:', [
                    'all_data' => $request->all(),
                    'drivers' => $request->input('drivers', []),
                    'driver_row_indexes' => $request->input('driver_row_indexes'),
                    'shipment_id' => $shipment->id
                ]);
                
                Log::info('Shipment update - Validated data:', [
                    'validated_data' => $request->validated(),
                    'drivers_validated' => $request->validated()['drivers'] ?? [],
                ]);
            }
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

    /**
     * Summary of show
     * @param \App\Models\Shipment $shipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Shipment $shipment)
    {
        return redirect()->back()->with('error', 'Trang không hợp lệ.');
    }
}

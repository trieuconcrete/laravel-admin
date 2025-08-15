<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\TollFee;
use App\Models\Vehicle;
use App\Enum\UserStatus;
use App\Models\Position;
use App\Models\CarRental;
use App\Models\TollStation;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Services\CarRentalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ShipmentDeductionType;
use App\Http\Requests\CarRental\StoreCarRentalRequest;
use App\Http\Requests\CarRental\UpdateCarRentalRequest;
use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Models\Customer;

class CarRentalController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\CarRentalService $carRentalService
     * @param \App\Repositories\Interface\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        protected CarRentalService $carRentalService,
        protected CustomerRepository $customerRepository,
        protected VehicleRepository $vehicleRepository
    ) {}

    // /**
    //  * Summary of index
    //  * @param \Illuminate\Http\Request $request
    //  * @return \Illuminate\Contracts\View\View
    //  */

    public function index(Request $request)
    {
        $customers = $this->customerRepository->all()->pluck('name', 'id');
        $carRentals = CarRental::with('customer')->paginate(10);
        $carRentalstatuses = CarRental::getStatuses();
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');

        return view('admin.car_rental.index', compact('carRentals', 'carRentalstatuses', 'customers', 'vehicleTypes'));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.car_rental.create');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\CarRental\StoreCarRentalRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCarRentalRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->carRentalService->create($request->all());
            DB::commit();
            return redirect()->route('admin.car-rental.index')->with([
                'success' => 'Tạo thông tin thuê xe thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.car-rental.index')->with([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $carRental = CarRental::with(['carRentalVehicles'])->findOrFail($id);
        if (request()->ajax()) {
            return view('admin.car_rental.partials.detail', compact('carRental'))->render();
        }

        return abort(404);    }

    public function edit($id)
    {
        $carRental = CarRental::findOrFail($id);
        $customers = $this->customerRepository->all()->pluck('name', 'id');
        $vehicles = Vehicle::with('vehicleType')->where('status', Vehicle::STATUS_ACTIVE)->get();
        $carRentalstatuses = CarRental::getStatuses();
        $carRentalVehicles = $carRental->carRentalVehicles;
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');

        // Get shipments instead of vehicle logs (Issue #180 implementation)
        $carRentalVehicleLogs = \App\Models\Shipment::where('car_rental_id', $id)
            ->where('shipment_type', \App\Models\Shipment::SHIPMENT_TYPE_MONTHLY_RENTAL)
            // ->where('is_car_rental', true)
            ->with(['driver', 'vehicle.vehicleType', 'tollFees'])
            ->latest('created_at')
            ->get();

        // Calculate total toll fees for each shipment
        foreach ($carRentalVehicleLogs as $shipment) {
            $shipment->total_toll_fee = $shipment->tollFees->sum('fee_amount');
        }

        // Thêm danh sách drivers cho form (Issue #180 requirement)
        $drivers = \App\Models\User::whereIn('role', ['driver', 'assistant', 'helper'])
            ->where('status', \App\Enum\UserStatus::ACTIVE)
            ->whereHas('position', function ($query) {
                $query->where('code', \App\Models\Position::POSITION_TX);
            })
            ->select('id', 'full_name', 'employee_code')
            ->get();

            // dd($carRentalVehicleLogs);
        return view('admin.car_rental.edit', compact(
            'carRental',
            'customers',
            'carRentalVehicles',
            'vehicleTypes',
            'carRentalstatuses',
            'carRentalVehicleLogs', // Now contains shipments, but keeping same variable name for view compatibility
            'vehicles',
            'drivers' // Thêm drivers vào compact
        ));
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\CarRental\UpdateCarRentalRequest $request
     * @param \App\Models\CarRental $CarRental
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateCarRentalRequest $request, CarRental $carRental)
    {
        DB::beginTransaction();
        try {
            // Log the request data for debugging
            DB::commit();
            $this->carRentalService->update($carRental->id, $request->all());
            return redirect()->route('admin.car-rental.edit', $carRental->id)->with('success', 'Cập nhật thông tin thuê xe thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CarRental update failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\CarRental $CarRental
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CarRental $carRental)
    {
        DB::beginTransaction();
        try {
            $carRental->carRentalVehicles()->delete();
            $carRental->delete();
            DB::commit();
            return back()->with('success', 'Xóa thông tin thuê xe thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Store a new vehicle log via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCarRentalVehicleLog(Request $request)
    {
        try {
            // Clean formatted numbers before validation
            $data = $request->all();
            $data['start_odometer'] = str_replace(',', '', $data['start_odometer']);
            $data['end_odometer'] = str_replace(',', '', $data['end_odometer']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);
            
            // Xử lý loại bỏ dấu phẩy từ driver deductions
            if (!empty($data['drivers'])) {
                foreach ($data['drivers'] as &$driver) {
                    if (!empty($driver['deductions'])) {
                        foreach ($driver['deductions'] as $key => &$value) {
                            if (is_string($value) && is_numeric($key)) {
                                $value = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            
            // Xử lý loại bỏ dấu phẩy từ driver PX deductions  
            if (!empty($data['driverPXs'])) {
                foreach ($data['driverPXs'] as &$driverPX) {
                    if (!empty($driverPX['deductions'])) {
                        foreach ($driverPX['deductions'] as $key => &$value) {
                            if (is_string($value) && is_numeric($key)) {
                                $value = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            
            // Xử lý loại bỏ dấu phẩy từ car rental deductions
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $key => &$value) {
                    if (is_string($value) && is_numeric($key)) {
                        $value = str_replace(',', '', $value);
                    }
                }
            }
            
            // Xử lý loại bỏ dấu phẩy từ unit_price_for_car_rental
            if (!empty($data['unit_price_for_car_rental'])) {
                $data['unit_price_for_car_rental'] = str_replace(',', '', $data['unit_price_for_car_rental']);
            }
            
            // Clean toll fee amounts
            if (!empty($data['toll_fees'])) {
                foreach ($data['toll_fees'] as &$tollFee) {
                    if (!empty($tollFee['fee_amount'])) {
                        $tollFee['fee_amount'] = str_replace(',', '', $tollFee['fee_amount']);
                    }
                }
            }

            // Validate request data
            $validated = validator($data, [
                'car_rental_id' => 'required|exists:car_rentals,id',
                'customer_id' => 'required|exists:customers,id',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'drivers' => 'nullable|array',
                'drivers.*.user_id' => 'nullable|exists:users,id',
                'drivers.*.deductions' => 'nullable|array',
                'drivers.*.deductions.*' => 'nullable',
                'driverPXs' => 'nullable|array',
                'driverPXs.*.user_id' => 'nullable|exists:users,id',
                'driverPXs.*.deductions' => 'nullable|array',
                'driverPXs.*.deductions.*' => 'nullable',
                'is_car_rental_value' => 'nullable|in:0,1',
                'run_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_location' => 'nullable|string|max:255',
                'end_location' => 'nullable|string|max:255',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'is_overtime_at_noon' => 'nullable|boolean',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'toll_fees' => 'nullable|array',
                'toll_fees.*.station_name' => 'nullable|string|max:255',
                'toll_fees.*.transaction_code' => 'nullable|string|max:255',
                'toll_fees.*.fee_amount' => 'nullable|numeric|min:0',
                'toll_fees.*.notes' => 'nullable|string',
                'deduction_type_ids' => 'nullable|array',
                'deduction_type_ids.*' => 'nullable|exists:shipment_deduction_types,id',
                'deductions' => 'nullable|array',
                'deductions.*' => 'nullable|numeric|min:0',
                'unit_price_for_car_rental' => 'nullable|numeric|min:0'
            ])->validate();

            $validated['overtime_rate'] = $validated['overtime_rate'] ?? 50000;
            $validated['parking_fee'] = isset($validated['parking_fee']) ? abs((float)$validated['parking_fee']) : 0;

            // Tính toán thời gian và quãng đường
            $startDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['start_time']);
            $endDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['end_time']);
            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);

            // Tính overtime_hours (chỉ tính sau 17:30)
            $overtimeHours = 0;
            $overtimeRate = $validated['overtime_rate'] ?? 0;
            if ($overtimeRate > 0) {
                $overtimeStart = \Carbon\Carbon::parse($validated['run_date'] . ' 17:30');
                if ($endDateTime->greaterThan($overtimeStart)) {
                    $effectiveStart = $startDateTime->greaterThan($overtimeStart) ? $startDateTime : $overtimeStart;
                    $overtimeHours = abs($endDateTime->floatDiffInRealHours($effectiveStart));
                }
                
                // Thêm tăng ca trưa 1h nếu có chọn checkbox (theo yêu cầu issue #180)
                if (!empty($validated['is_overtime_at_noon'])) {
                    $overtimeHours += 1;
                }
            }
            $totalOvertimeCost = abs($overtimeRate * $overtimeHours);

            // Get vehicle và car rental để lấy thông tin
            $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);
            $carRental = \App\Models\CarRental::with('customer')->findOrFail($validated['car_rental_id']);

            // Kiểm tra có phải xe HPL thuê không - ưu tiên từ form, fallback về vehicle property
            $isHplRental = isset($validated['is_car_rental_value']) ? (bool)$validated['is_car_rental_value'] : $vehicle->is_car_rental;
            $driverId = null;
            
            // Chỉ bắt buộc chọn tài xế nếu là xe công ty (không phải xe thuê)
            if (!$isHplRental) {
                // Kiểm tra nhiều cách có thể có của drivers data
                $hasDriverSelected = false;
                
                if (isset($validated['drivers']) && is_array($validated['drivers'])) {
                    foreach ($validated['drivers'] as $driver) {
                        if (isset($driver['user_id']) && !empty($driver['user_id'])) {
                            $driverId = $driver['user_id'];
                            $hasDriverSelected = true;
                            break;
                        }
                    }
                }
                
                if (!$hasDriverSelected) {
                    return back()->withErrors([
                        'drivers.0.user_id' => 'Vui lòng chọn tài xế cho xe công ty này.'
                    ])->withInput();
                }
            } else {
                // Xe thuê - driver_id có thể null hoặc có giá trị từ drivers array
                if (isset($validated['drivers']) && is_array($validated['drivers'])) {
                    foreach ($validated['drivers'] as $driver) {
                        if (isset($driver['user_id']) && !empty($driver['user_id'])) {
                            $driverId = $driver['user_id'];
                            break;
                        }
                    }
                }
                // $driverId có thể vẫn null nếu không có driver nào được chọn
            }

            DB::beginTransaction();

            // Tạo Shipment mới (đáp ứng yêu cầu issue #180)
            $shipmentData = [
                'shipment_code' => \App\Models\Shipment::generateShipmentCode(),
                'customer_id' => $carRental->customer_id,
                'origin' => $validated['start_location'] ?? 'Điểm bắt đầu',
                'destination' => $validated['end_location'] ?? 'Điểm kết thúc',
                'departure_time' => $startDateTime,
                'estimated_arrival_time' => $endDateTime,
                'cargo_description' => 'Thuê xe - ' . ($carRental->description ?? 'Dịch vụ thuê xe'),
                'driver_id' => $driverId,
                'vehicle_id' => $validated['vehicle_id'],
                'distance' => $totalDistance,
                'status' => \App\Models\Shipment::STATUS_COMPLETED,
                'is_car_rental' => $isHplRental, // Sử dụng $isHplRental thay vì hard-code true
                'shipment_type' => \App\Models\Shipment::SHIPMENT_TYPE_MONTHLY_RENTAL, // Set = 2 theo yêu cầu
                'created_by' => auth('admin')->id(),
                
                // Thông tin từ vehicle log
                'car_rental_id' => $validated['car_rental_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'run_date' => $validated['run_date'],
                'overtime_hours' => $overtimeHours,
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'is_overtime_at_noon' => $validated['is_overtime_at_noon'] ?? false,
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'unit_price_for_car_rental' => $validated['is_car_rental_value'] ? $validated['unit_price_for_car_rental'] : null
            ];

            $shipment = \App\Models\Shipment::create($shipmentData);

            // Create toll fees if provided
            if (!empty($validated['toll_fees'])) {
                foreach ($validated['toll_fees'] as $tollFeeData) {
                    if (!empty($tollFeeData['station_name']) && !empty($tollFeeData['fee_amount'])) {
                        \App\Models\TollFee::create([
                            'vehicle_log_id' => null,
                            'shipment_id' => $shipment->id,
                            'station_name' => $tollFeeData['station_name'],
                            'transaction_code' => $tollFeeData['transaction_code'] ?? null,
                            'fee_amount' => abs((float)$tollFeeData['fee_amount']),
                            'notes' => $tollFeeData['notes'] ?? null
                        ]);
                    }
                }
            }

            // Lưu driver deductions (chỉ khi không phải xe thuê)
            if (!empty($validated['drivers']) && !($validated['is_car_rental_value'] ?? false)) {
                Log::info('Store: Processing drivers data:', ['drivers' => $validated['drivers']]);
                
                foreach ($validated['drivers'] as $person) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($person['user_id']) && is_numeric($person['user_id']) && (int)$person['user_id'] > 0) {
                        $user_id = (int)$person['user_id'];
                        Log::info('Store: Processing driver:', ['user_id' => $user_id, 'driver_data' => $person]);
                        
                        // Luôn tạo một driver deduction cơ bản để đánh dấu driver này
                        $basicDriverType = \App\Models\ShipmentDeductionType::where('type', 'driver')->first();
                        if ($basicDriverType) {
                            // Lấy is_main_driver từ deductions array
                            $isMainDriver = false;
                            if (isset($person['deductions']['is_main_driver'])) {
                                $isMainDriver = (bool) $person['deductions']['is_main_driver'];
                            }
                            
                            $basicDriverDeduction = \App\Models\ShipmentDeduction::create([
                                'user_id' => $user_id,
                                'shipment_id' => $shipment->id,
                                'shipment_deduction_type_id' => $basicDriverType->id,
                                'amount' => 0, // Không có amount cho driver cơ bản
                                'is_main_driver' => $isMainDriver,
                                'notes' => $person['deductions']['Ghi chú'] ?? null
                            ]);
                            
                            Log::info('Store: Created basic driver deduction:', [
                                'deduction_id' => $basicDriverDeduction->id,
                                'type_id' => $basicDriverType->id,
                                'user_id' => $user_id,
                                'is_main_driver' => $isMainDriver
                            ]);
                        }
                        
                        if (!empty($person['deductions'])) {
                            // Extract notes và is_main_driver từ deductions array
                            $notes = $person['deductions']['Ghi chú'] ?? null;
                            $isMainDriver = (bool) ($person['deductions']['is_main_driver'] ?? false);
                            
                            // Remove non-deduction fields
                            $deductions = $person['deductions'];
                            unset($deductions['Ghi chú'], $deductions['is_main_driver']);
                            
                            foreach ($deductions as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    // Xử lý amount - loại bỏ dấu phẩy và parse số
                                    $cleanAmount = is_string($amount) ? str_replace(',', '', $amount) : $amount;
                                    $parsedAmount = (float)$cleanAmount;
                                    
                                    $deduction = \App\Models\ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id,
                                        'amount' => $parsedAmount,
                                        'notes' => $notes,
                                        'is_main_driver' => $isMainDriver
                                    ]);
                                        
                                    Log::info('Store: Created driver deduction:', [
                                        'deduction_id' => $deduction->id,
                                        'user_id' => $user_id,
                                        'type_id' => $deduction_type_id,
                                        'amount' => $parsedAmount,
                                        'is_main_driver' => $isMainDriver,
                                        'notes' => $notes
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Lưu driver PX deductions (chỉ khi không phải xe thuê)
            if (!empty($validated['driverPXs']) && !($validated['is_car_rental_value'] ?? false)) {
                Log::info('Store: Processing driver PX data:', ['driverPXs' => $validated['driverPXs']]);
                
                foreach ($validated['driverPXs'] as $driverPX) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($driverPX['user_id']) && is_numeric($driverPX['user_id']) && (int)$driverPX['user_id'] > 0) {
                        $user_id = (int)$driverPX['user_id'];
                        Log::info('Store: Processing driver PX:', ['user_id' => $user_id, 'driver_px_data' => $driverPX]);
                        
                        if (!empty($driverPX['deductions'])) {
                            // Extract notes từ deductions array
                            $notes = $driverPX['deductions']['Ghi chú'] ?? null;
                            $deductions = $driverPX['deductions'];
                            unset($deductions['Ghi chú']);
                            
                            foreach ($deductions as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    $deduction = \App\Models\ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id,
                                        'amount' => (float)str_replace(',', '', $amount),
                                        'is_main_driver' => false,
                                        'notes' => $notes
                                    ]);
                                    
                                    Log::info('Store: Created driver PX deduction:', [
                                        'deduction_id' => $deduction->id,
                                        'user_id' => $user_id,
                                        'type_id' => $deduction_type_id,
                                        'amount' => $amount,
                                        'notes' => $notes
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Lưu car rental deductions (chỉ khi là xe thuê)
            if (!empty($validated['deduction_type_ids']) && !empty($validated['deductions']) && ($validated['is_car_rental_value'] ?? false)) {
                Log::info('Store: Processing car rental deductions:', [
                    'deduction_type_ids' => $validated['deduction_type_ids'],
                    'deductions' => $validated['deductions']
                ]);
                
                foreach ($validated['deduction_type_ids'] as $index => $deductionTypeId) {
                    if (isset($validated['deductions'][$deductionTypeId]) && !empty($validated['deductions'][$deductionTypeId])) {
                        $amount = str_replace(',', '', $validated['deductions'][$deductionTypeId]);
                        $parsedAmount = (float)$amount;
                        
                        if ($parsedAmount > 0) {
                            $carRentalDeduction = \App\Models\ShipmentDeduction::create([
                                'shipment_id' => $shipment->id,
                                'shipment_deduction_type_id' => (int)$deductionTypeId,
                                'amount' => $parsedAmount,
                                'user_id' => null, // Không có user cụ thể cho car rental deductions
                                'is_main_driver' => false,
                                'notes' => null
                            ]);
                            
                            Log::info('Store: Created car rental deduction:', [
                                'deduction_id' => $carRentalDeduction->id,
                                'type_id' => $deductionTypeId,
                                'amount' => $parsedAmount
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // Redirect về trang edit car-rental với tab "Nhật ký lộ trình xe"
            return redirect()->route('admin.car-rental.edit', $carRental->id)
                ->with('success', 'Tạo nhật ký xe thành công')
                ->with('active_tab', 'vehicle-logs'); // Tab "Nhật ký lộ trình xe"
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Có lỗi validation xảy ra');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle log creation failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Get shipment data for editing
     * 
     * @param int $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editShipmentVehicleLog($shipmentId)
    {
        try {
            $shipment = \App\Models\Shipment::with([
                'tollFees',
                'vehicle',
                'driver',
                'customer',
                'carRental'
            ])->findOrFail($shipmentId);
            
            // Lấy thông tin car rental
            $carRental = \App\Models\CarRental::findOrFail($shipment->car_rental_id);
            
            // Lấy danh sách customers
            $customers = \App\Models\Customer::pluck('name', 'id');
            
            // Lấy danh sách vehicles
            $vehicles = \App\Models\Vehicle::with('vehicleType')->get();
            
            // Lấy danh sách users (tài xế)
            $users = \App\Models\User::where('role', 'driver')->pluck('full_name', 'id')->toArray();
            
            // Lấy danh sách userPXs (lơ xe)
            $userPXs = User::whereIn('role', ['driver', 'assistant', 'helper', 'staff'])
                ->where('status', UserStatus::ACTIVE)
                ->whereHas('position', function ($query) {
                    $query->whereIn('code', [Position::POSITION_PX, Position::POSITION_TX]);
                })
                ->pluck('full_name', 'id')
                ->toArray();
            
            // Lấy danh sách person deduction types
            $personDeductionTypes = \App\Models\ShipmentDeductionType::where('type', 'driver')->get();
            
            // Lấy danh sách sub person deduction types
            // $subPersonDeductionTypes = \App\Models\ShipmentDeductionType::where('type', 'driver_px')->get();
            
                        
            $subPersonDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_BUS_DRIVER)
                ->where('status', 'active')
                ->orderBy('order', 'asc')
                ->get();
            // Lấy shipment status
            $shipmentStatus = [
                'pending' => 'Tạo mới',
                'in_transit' => 'Đang vận chuyển',
                'cancelled' => 'Đã hủy',
                'delayed' => 'Bị trễ',
                'completed' => 'Hoàn thành'
            ];
            
            // Lấy deduction types
            $deductionTypes = \App\Models\ShipmentDeductionType::where('type', 'shipment')->get();
            
            // Lấy shipment deductions
            $shipmentDeductions = \App\Models\ShipmentDeduction::where('shipment_id', $shipment->id)
                ->pluck('amount', 'shipment_deduction_type_id');
            
            // Lấy car rental deductions (chỉ khi là xe thuê)
            $carRentalDeductionTypes = ShipmentDeductionType::where('type', ShipmentDeductionType::TYPE_CAR_RENTAL_EXPENSE)
                ->where('status', 'active')
                ->orderBy('order', 'asc')
                ->get();
            
            $carRentalDeductions = \App\Models\ShipmentDeduction::where('shipment_id', $shipment->id)
                ->whereNull('user_id') // Không có user cụ thể
                ->whereIn('shipment_deduction_type_id', $carRentalDeductionTypes->pluck('id'))
                ->get();
            
            // Debug log để xem car rental deductions được load
            Log::info('Car rental deductions loaded:', [
                'shipment_id' => $shipment->id,
                'car_rental_deduction_type_ids' => $carRentalDeductionTypes->pluck('id')->toArray(),
                'car_rental_deductions_count' => $carRentalDeductions->count(),
                'car_rental_deductions_data' => $carRentalDeductions->toArray()
            ]);
            
            // Lấy driver deductions - dựa vào user_id và không có deduction type cụ thể
            $driverDeductions = \App\Models\ShipmentDeduction::where('shipment_id', $shipment->id)
                ->whereNotNull('user_id')
                ->where(function($query) {
                    $query->whereNull('shipment_deduction_type_id')
                          ->orWhereIn('shipment_deduction_type_id', function($subQuery) {
                              $subQuery->select('id')
                                      ->from('shipment_deduction_types')
                                      ->where('type', 'driver');
                          });
                })
                ->get()
                ->groupBy('user_id');
            
            // Lấy driver PX deductions - dựa vào user_id và deduction type = 'driver_px'
            $driverPXDeductionTypeIds = $subPersonDeductionTypes->pluck('id');
            Log::info('Driver PX deduction type IDs from subPersonDeductionTypes:', ['ids' => $driverPXDeductionTypeIds->toArray()]);
            
            $driverPXDeductions = \App\Models\ShipmentDeduction::where('shipment_id', $shipment->id)
                ->whereNotNull('user_id')
                ->whereIn('shipment_deduction_type_id', $driverPXDeductionTypeIds)
                ->get()
                ->groupBy('user_id');
                
            Log::info('Driver PX deductions loaded:', [
                'count' => $driverPXDeductions->count(),
                'user_ids' => $driverPXDeductions->keys()->toArray(),
                'raw_deductions' => $driverPXDeductions->toArray()
            ]);
            
                // dd($subPersonDeductionTypes);
            // Debug log để xem deduction types và deductions
            Log::info('Driver deduction types and deductions:', [
                'driver_deductions_raw' => \App\Models\ShipmentDeduction::where('shipment_id', $shipment->id)->get()->toArray(),
                'driver_deductions_grouped' => $driverDeductions->toArray(),
                'driver_px_deductions_grouped' => $driverPXDeductions->toArray()
            ]);
            
            return view('admin.car_rental.shipments.edit', compact(
                'shipment',
                'carRental',
                'customers',
                'vehicles',
                'users',
                'userPXs',
                'personDeductionTypes',
                'subPersonDeductionTypes',
                'shipmentStatus',
                'deductionTypes',
                'shipmentDeductions',
                'driverDeductions',
                'driverPXDeductions',
                'carRentalDeductionTypes',
                'carRentalDeductions'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to get shipment for editing', ['error' => $e->getMessage()]);
            return back()->with('error', 'Không thể tải dữ liệu nhật ký xe: ' . $e->getMessage());
        }
    }

    /**
     * Update shipment vehicle log
     * 
     * @param Request $request
     * @param int $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShipmentVehicleLog(Request $request, $shipmentId)
    {
        try {
            $shipment = \App\Models\Shipment::findOrFail($shipmentId);
            $data = $request->all();
            
            // Debug log để xem dữ liệu được gửi lên
            Log::info('Update shipment vehicle log - Request data:', [
                'shipment_id' => $shipmentId,
                'status' => $data['status'] ?? 'not_set',
                'status_type' => gettype($data['status'] ?? 'not_set'),
                'is_car_rental_value' => $data['is_car_rental_value'] ?? 'not_set',
                'drivers' => $data['drivers'] ?? 'not_set',
                'driver_0_user_id' => $data['drivers']['0']['user_id'] ?? 'not_set',
                'all_data' => $data,
                'request_method' => $request->method(),
                'request_url' => $request->url(),
                'request_headers' => $request->headers->all()
            ]);
            
            $data['start_odometer'] = str_replace(',', '', $data['start_odometer']);
            $data['end_odometer'] = str_replace(',', '', $data['end_odometer']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);
            $data['max_distance'] = str_replace(',', '', $data['max_distance'] ?? '');
            $data['over_distance_fee_per_km'] = str_replace(',', '', $data['over_distance_fee_per_km'] ?? '');
            
            // Clean deductions data - remove commas from numeric values
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $key => &$value) {
                    if (is_string($value) && is_numeric(str_replace(',', '', $value))) {
                        $value = str_replace(',', '', $value);
                    }
                }
            }
            
            // Xử lý loại bỏ dấu phẩy từ driver deductions
            if (!empty($data['drivers'])) {
                foreach ($data['drivers'] as &$driver) {
                    if (!empty($driver['deductions'])) {
                        foreach ($driver['deductions'] as $key => &$value) {
                            if (is_string($value) && is_numeric($key)) {
                                $value = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            
            // Xử lý loại bỏ dấu phẩy từ driver PX deductions  
            if (!empty($data['driverPXs'])) {
                foreach ($data['driverPXs'] as &$driverPX) {
                    if (!empty($driverPX['deductions'])) {
                        foreach ($driverPX['deductions'] as $key => &$value) {
                            if (is_string($value) && is_numeric($key)) {
                                $value = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            
            if (!empty($data['toll_fees'])) {
                foreach ($data['toll_fees'] as &$tollFee) {
                    if (!empty($tollFee['fee_amount'])) {
                        $tollFee['fee_amount'] = str_replace(',', '', $tollFee['fee_amount']);
                    }
                }
            }
            
            // Clean unit_price_for_car_rental if it exists
            if (!empty($data['unit_price_for_car_rental'])) {
                $data['unit_price_for_car_rental'] = str_replace(',', '', $data['unit_price_for_car_rental']);
            }
            
            $validated = validator($data, [
                'car_rental_id' => 'required|exists:car_rentals,id',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'drivers' => 'nullable|array',
                'drivers.*.user_id' => 'nullable|exists:users,id',
                'drivers.*.deductions' => 'nullable|array',
                'drivers.*.deductions.*' => 'nullable',
                'driverPXs' => 'nullable|array',
                'driverPXs.*.user_id' => 'nullable|exists:users,id',
                'driverPXs.*.deductions' => 'nullable|array',
                'driverPXs.*.deductions.*' => 'nullable',
                'is_car_rental_value' => 'required|in:0,1',
                'run_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_location' => 'nullable|string|max:255',
                'end_location' => 'nullable|string|max:255',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'is_overtime_at_noon' => 'nullable|boolean',
                'status' => 'required|in:pending,in_transit,cancelled,delayed,completed',
                'notes' => 'nullable|string',
                'toll_fees' => 'nullable|array',
                'toll_fees.*.station_name' => 'nullable|string|max:255',
                'toll_fees.*.transaction_code' => 'nullable|string|max:255',
                'toll_fees.*.fee_amount' => 'nullable|numeric|min:0',
                'toll_fees.*.notes' => 'nullable|string',
                'deduction_type_ids' => 'nullable|array',
                'deduction_type_ids.*' => 'nullable|exists:shipment_deduction_types,id',
                'deductions' => 'nullable|array',
                'deductions.*' => 'nullable|numeric|min:0',
                'unit_price_for_car_rental' => 'nullable|numeric|min:0',
            ])->validate();
            
            $validated['overtime_rate'] = $validated['overtime_rate'] ?? 50000;
            $validated['parking_fee'] = isset($validated['parking_fee']) ? abs((float)$validated['parking_fee']) : 0;

            // Get vehicle và car rental để lấy thông tin
            $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);
            $carRental = \App\Models\CarRental::with('customer')->findOrFail($validated['car_rental_id']);

            // Kiểm tra có phải xe HPL thuê không
            $isHplRental = (bool)$validated['is_car_rental_value'];
            $driverId = null;
            
            // Debug log drivers data structure
            Log::info('Validation drivers data:', [
                'is_car_rental_value' => $validated['is_car_rental_value'],
                'isHplRental' => $isHplRental,
                'drivers_exists' => isset($validated['drivers']),
                'drivers_data' => $validated['drivers'] ?? 'not_set',
                'drivers_0_exists' => isset($validated['drivers'][0]),
                'drivers_0_user_id_exists' => isset($validated['drivers'][0]['user_id']),
                'drivers_0_user_id_value' => $validated['drivers'][0]['user_id'] ?? 'not_set'
            ]);
            
            // Chỉ bắt buộc chọn tài xế nếu là xe công ty (không phải xe thuê)
            if (!$isHplRental) {
                // Kiểm tra nhiều cách có thể có của drivers data
                $hasDriverSelected = false;
                
                if (isset($validated['drivers']) && is_array($validated['drivers'])) {
                    foreach ($validated['drivers'] as $driver) {
                        if (isset($driver['user_id']) && !empty($driver['user_id'])) {
                            $driverId = $driver['user_id'];
                            $hasDriverSelected = true;
                            break;
                        }
                    }
                }
                
                if (!$hasDriverSelected) {
                    return back()->withErrors([
                        'drivers.0.user_id' => 'Vui lòng chọn tài xế cho xe công ty này.'
                    ])->withInput();
                }
            } else {
                // Xe thuê - driver_id có thể null hoặc có giá trị từ drivers array
                if (isset($validated['drivers']) && is_array($validated['drivers'])) {
                    foreach ($validated['drivers'] as $driver) {
                        if (isset($driver['user_id']) && !empty($driver['user_id'])) {
                            $driverId = $driver['user_id'];
                            break;
                        }
                    }
                }
                // $driverId có thể vẫn null nếu không có driver nào được chọn
            }

            // Tính toán lại các giá trị
            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);
            $startDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['start_time']);
            $endDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['end_time']);
            
            // Tính overtime_hours (chỉ tính sau 17:30)
            $overtimeHours = 0;
            $overtimeRate = $validated['overtime_rate'] ?? 0;
            if ($overtimeRate > 0) {
                $overtimeStart = \Carbon\Carbon::parse($validated['run_date'] . ' 17:30');
                if ($endDateTime->greaterThan($overtimeStart)) {
                    $effectiveStart = $startDateTime->greaterThan($overtimeStart) ? $startDateTime : $overtimeStart;
                    $overtimeHours = abs($endDateTime->floatDiffInRealHours($effectiveStart));
                }
                
                // Thêm tăng ca trưa 1h nếu có chọn checkbox (theo yêu cầu issue #180)
                if (!empty($validated['is_overtime_at_noon'])) {
                    $overtimeHours += 1;
                }
            }
            $totalOvertimeCost = abs($overtimeRate * $overtimeHours);

            DB::beginTransaction();

            // Update Shipment
            $shipmentData = [
                'origin' => $validated['start_location'] ?? $shipment->origin,
                'destination' => $validated['end_location'] ?? $shipment->destination,
                'departure_time' => $startDateTime,
                'estimated_arrival_time' => $endDateTime,
                'driver_id' => $driverId, // Sử dụng $driverId đã được set ở trên (có thể null nếu is_car_rental = true)
                'vehicle_id' => $validated['vehicle_id'],
                'distance' => $totalDistance,
                'status' => $validated['status'],
                'is_car_rental' => $isHplRental,
                
                // Thông tin từ vehicle log
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'run_date' => $validated['run_date'],
                'overtime_hours' => $overtimeHours,
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'is_overtime_at_noon' => $validated['is_overtime_at_noon'] ?? false,
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'unit_price_for_car_rental' => $validated['is_car_rental_value'] ? $validated['unit_price_for_car_rental'] : null
            ];
            $shipment->update($shipmentData);
            
            // Debug log sau khi update
            Log::info('Update shipment vehicle log - After update:', [
                'shipment_id' => $shipment->id,
                'status' => $shipment->status,
                'driver_id' => $shipment->driver_id,
                'updated_data' => $shipmentData
            ]);

            // Xóa toàn bộ deductions cũ và lưu lại danh sách mới
            $shipment->shipmentDeductions()->delete();
            Log::info('Deleted old deductions for shipment:', ['shipment_id' => $shipment->id]);

            // Lưu driver deductions (gộp cả format mới và cũ)
            if (!empty($validated['drivers']) && is_array($validated['drivers']) && !($validated['is_car_rental_value'] ?? false)) {
                // Thu thập tất cả driver deductions từ format cũ (drivers_X_deductions_Y)
                $driverDeductionsFromOldFormat = [];
                foreach ($data as $key => $value) {
                    if (preg_match('/^drivers_(\d+)_deductions_(\d+)$/', $key, $matches)) {
                        $driverIndex = $matches[1];
                        $deductionTypeId = $matches[2];
                        if (!isset($driverDeductionsFromOldFormat[$driverIndex])) {
                            $driverDeductionsFromOldFormat[$driverIndex] = [];
                        }
                        $driverDeductionsFromOldFormat[$driverIndex][$deductionTypeId] = $value;
                    }
                }
                
                Log::info('Processing driver deductions:', [
                    'drivers' => $validated['drivers'],
                    'old_format_deductions' => $driverDeductionsFromOldFormat
                ]);
                
                foreach ($validated['drivers'] as $driverIndex => $driver) {
                    if (!empty($driver['user_id'])) {
                        $userId = $driver['user_id'];
                        $deductions = $driver['deductions'] ?? [];
                        
                        // Merge deductions từ format cũ vào
                        if (isset($driverDeductionsFromOldFormat[$driverIndex])) {
                            foreach ($driverDeductionsFromOldFormat[$driverIndex] as $typeId => $amount) {
                                // Chỉ merge nếu chưa có trong format mới
                                if (!isset($deductions[$typeId])) {
                                    $deductions[$typeId] = $amount;
                                }
                            }
                        }
                        
                        // Xử lý is_main_driver và notes
                        $isMainDriver = (bool) ($deductions['is_main_driver'] ?? false);
                        $notes = $deductions['Ghi chú'] ?? null;
                        
                        // Tạo basic driver deduction
                        $basicDeduction = \App\Models\ShipmentDeduction::create([
                            'user_id' => $userId,
                            'shipment_id' => $shipment->id,
                            'shipment_deduction_type_id' => null, // Không có type cụ thể
                            'amount' => 0, // Không có amount cụ thể
                            'is_main_driver' => $isMainDriver,
                            'notes' => $notes
                        ]);
                        
                        Log::info('Created basic driver deduction:', [
                            'deduction_id' => $basicDeduction->id,
                            'user_id' => $userId,
                            'is_main_driver' => $isMainDriver,
                            'notes' => $notes
                        ]);
                        
                        // Xử lý tất cả deductions (đã merge từ cả 2 format)
                        foreach ($deductions as $deductionTypeId => $amount) {
                            if (is_numeric($deductionTypeId) && (int)$deductionTypeId > 0 && !empty($amount)) {
                                $cleanAmount = str_replace(',', '', $amount);
                                $parsedAmount = (float)$cleanAmount;
                                
                                if ($parsedAmount > 0) {
                                    $driverDeduction = \App\Models\ShipmentDeduction::create([
                                        'user_id' => $userId,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deductionTypeId,
                                        'amount' => $parsedAmount,
                                        'is_main_driver' => false,
                                        'notes' => null
                                    ]);
                                    
                                    Log::info('Created driver deduction:', [
                                        'deduction_id' => $driverDeduction->id,
                                        'user_id' => $userId,
                                        'type_id' => $deductionTypeId,
                                        'amount' => $parsedAmount
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Xóa và cập nhật lại các phụ cấp tài xế phụ cấp (chỉ khi không phải xe thuê)
            if (!empty($validated['driverPXs']) && !($validated['is_car_rental_value'] ?? false)) {
                Log::info('Processing driver PX data:', ['driverPXs' => $validated['driverPXs']]);
                
                foreach ($validated['driverPXs'] as $driverPX) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($driverPX['user_id']) && is_numeric($driverPX['user_id']) && (int)$driverPX['user_id'] > 0) {
                        $user_id = (int)$driverPX['user_id'];
                        Log::info('Processing driver PX:', ['user_id' => $user_id, 'driver_px_data' => $driverPX]);
                        
                        if (!empty($driverPX['deductions'])) {
                            // Extract notes from deductions array if it exists
                            $notes = null;
                            if (isset($driverPX['deductions']['Ghi chú'])) {
                                $notes = $driverPX['deductions']['Ghi chú'];
                                unset($driverPX['deductions']['Ghi chú']); // Remove notes from deductions array
                            }
                            
                            foreach ($driverPX['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    $deduction = \App\Models\ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                        'is_main_driver' => false,
                                        'notes' => $notes // Add notes field
                                    ]);
                                    
                                    Log::info('Created driver PX deduction:', [
                                        'deduction_id' => $deduction->id,
                                        'user_id' => $user_id,
                                        'type_id' => $deduction_type_id,
                                        'amount' => $amount,
                                        'notes' => $notes
                                    ]);
                                }
                            }
                        }
                    }
                }
            } else {
                Log::info('No driver PX data to process or is_car_rental = true');
            }

            // Lưu car rental deductions (chỉ khi là xe thuê)
            if (!empty($validated['deduction_type_ids']) && !empty($validated['deductions']) && ($validated['is_car_rental_value'] ?? false)) {
                Log::info('Store: Processing car rental deductions:', [
                    'deduction_type_ids' => $validated['deduction_type_ids'],
                    'deductions' => $validated['deductions']
                ]);
                
                foreach ($validated['deduction_type_ids'] as $index => $deductionTypeId) {
                    if (isset($validated['deductions'][$deductionTypeId]) && !empty($validated['deductions'][$deductionTypeId])) {
                        $amount = str_replace(',', '', $validated['deductions'][$deductionTypeId]);
                        $parsedAmount = (float)$amount;
                        
                        if ($parsedAmount > 0) {
                            $carRentalDeduction = \App\Models\ShipmentDeduction::create([
                                'shipment_id' => $shipment->id,
                                'shipment_deduction_type_id' => (int)$deductionTypeId,
                                'amount' => $parsedAmount,
                                'user_id' => null, // Không có user cụ thể cho car rental deductions
                                'is_main_driver' => false,
                                'notes' => null
                            ]);
                            
                            Log::info('Store: Created car rental deduction:', [
                                'deduction_id' => $carRentalDeduction->id,
                                'type_id' => $deductionTypeId,
                                'amount' => $parsedAmount
                            ]);
                        }
                    }
                }
            }
            
            // Xóa toàn bộ toll_fees cũ và lưu lại danh sách mới
            $shipment->tollFees()->delete();
            if (!empty($validated['toll_fees'])) {
                foreach ($validated['toll_fees'] as $tollFeeData) {
                    if (!empty($tollFeeData['station_name']) && !empty($tollFeeData['fee_amount'])) {
                        \App\Models\TollFee::create([
                            'vehicle_log_id' => null,
                            'shipment_id' => $shipment->id,
                            'station_name' => $tollFeeData['station_name'],
                            'transaction_code' => $tollFeeData['transaction_code'] ?? null,
                            'fee_amount' => abs((float)$tollFeeData['fee_amount']),
                            'notes' => $tollFeeData['notes'] ?? null
                        ]);
                    }
                }
            }

            DB::commit();

            // Redirect về trang edit car-rental với tab "Nhật ký lộ trình xe"
            return redirect()->route('admin.car-rental.edit', $carRental->id)
                ->with('success', 'Cập nhật nhật ký xe thành công')
                ->with('active_tab', 'vehicle-logs'); // Tab "Nhật ký lộ trình xe"
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Có lỗi validation xảy ra');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment update failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa shipment vehicle log
     */
    public function destroyShipmentVehicleLog($shipmentId)
    {
        try {
            DB::beginTransaction();
            
            $shipment = \App\Models\Shipment::findOrFail($shipmentId);
            
            // Xóa toll fees liên quan
            $shipment->tollFees()->delete();
            
            // Xóa shipment
            $shipment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa nhật ký xe thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete shipment failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhật ký xe!'
            ], 500);
        }
    }

    /**
     * Get vehicle log from shipment ID for editing
     * 
     * @param int $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editCarRentalVehicleLogFromShipment($shipmentId)
    {
        try {
            return $this->editShipmentVehicleLog($shipmentId);
        } catch (\Exception $e) {
            Log::error('Failed to get vehicle log from shipment', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải dữ liệu nhật ký xe'
            ], 500);
        }
    }

    /**
     * Delete vehicle log from shipment ID
     * 
     * @param int $shipmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyCarRentalVehicleLogFromShipment($shipmentId)
    {
        try {
            return $this->destroyShipmentVehicleLog($shipmentId);
        } catch (\Exception $e) {
            Log::error('Failed to delete vehicle log from shipment', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa nhật ký xe'
            ], 500);
        }
    }

    /**
     * Download all vehicle logs for a car rental as an Excel file
     * Updated to use Shipments instead of CarRentalVehicleLog
     *
     * @param int $car_rental_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadVehicleLog($car_rental_id)
    {
        $carRental = CarRental::with('customer')->findOrFail($car_rental_id);
        
        // Get shipments instead of vehicle logs (Issue #180 implementation)
        $shipments = \App\Models\Shipment::where('car_rental_id', $car_rental_id)
            ->where('shipment_type', \App\Models\Shipment::SHIPMENT_TYPE_MONTHLY_RENTAL)
            // ->where('is_car_rental', true) // Removed to match edit view logic - get all shipments for car rental
            ->with(['driver', 'vehicle', 'tollFees'])
            ->orderBy('run_date', 'asc')
            ->get();
            
        // Debug log to check shipments count
        Log::info('Download vehicle log - Shipments loaded:', [
            'car_rental_id' => $car_rental_id,
            'shipments_count' => $shipments->count(),
            'shipments_data' => $shipments->map(function($shipment) {
                return [
                    'id' => $shipment->id,
                    'run_date' => $shipment->run_date,
                    'is_car_rental' => $shipment->is_car_rental,
                    'shipment_type' => $shipment->shipment_type
                ];
            })->toArray()
        ]);

        // Group toll fees by run_date for easy access
        $tollFeesByDate = collect();
        foreach ($shipments as $shipment) {
            $dateKey = \Carbon\Carbon::parse($shipment->run_date)->format('Y-m-d');
            if (!$tollFeesByDate->has($dateKey)) {
                $tollFeesByDate->put($dateKey, collect());
            }
            
            // Add shipment reference to each toll fee for vehicle lookup
            foreach ($shipment->tollFees as $tollFee) {
                // Ensure shipment_id is set for vehicle lookup
                if (!$tollFee->shipment_id) {
                    $tollFee->shipment_id = $shipment->id;
                }
                $tollFeesByDate->get($dateKey)->push($tollFee);
            }
        }

        $month = now()->format('m/Y');
        $fileName = 'bien_ban_nhat_ky_lo_trinh_xe_' . $carRental->id . '_' . str_replace('/', '', $month) . '.xlsx';

        return Excel::download(new \App\Exports\ShipmentVehicleLogExport($carRental, $shipments, $tollFeesByDate, $month), $fileName);
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function shipmentCreate($id)
    {
        // Lấy thông tin car rental để có customer_id
        $carRental = CarRental::with('customer')->findOrFail($id);
        
        $vehicles = Vehicle::with(['vehicleType', 'driver'])->where('status', Vehicle::STATUS_ACTIVE)->get();
        
        // Get customers data
        $customers = Customer::where('is_active', 1)->pluck('name', 'id');
        
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
            
        return view('admin.car_rental.shipments.create', compact(
            'carRental',
            'customers',
            'vehicles',
            'users', 
            'deductionTypes', 
            'personDeductionTypes', 
            'subPersonDeductionTypes', 
            'userPXs',
            'carRentalDeductionTypes'
        ));
    }
}

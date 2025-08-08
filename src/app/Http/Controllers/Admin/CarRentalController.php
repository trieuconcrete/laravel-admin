<?php

namespace App\Http\Controllers\Admin;

use App\Models\CarRental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interface\CustomerRepositoryInterface as CustomerRepository;
use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Http\Requests\CarRental\UpdateCarRentalRequest;
use App\Services\CarRentalService;
use App\Http\Requests\CarRental\StoreCarRentalRequest;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TollStation;
use App\Models\TollFee;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Vehicle;

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
            ->where('is_car_rental', true)
            ->with(['driver', 'vehicle.vehicleType', 'tollFees'])
            ->latest('run_date')
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
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'driver_id' => 'nullable|exists:users,id', // Thêm validate cho driver_id
                'run_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_location' => 'nullable|string|max:255',
                'end_location' => 'nullable|string|max:255',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'toll_fees' => 'nullable|array',
                'toll_fees.*.station_name' => 'nullable|string|max:255',
                'toll_fees.*.transaction_code' => 'nullable|string|max:255',
                'toll_fees.*.fee_amount' => 'nullable|numeric|min:0',
                'toll_fees.*.notes' => 'nullable|string'
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
            }
            $totalOvertimeCost = abs($overtimeRate * $overtimeHours);

            // Get vehicle và car rental để lấy thông tin
            $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);
            $carRental = \App\Models\CarRental::with('customer')->findOrFail($validated['car_rental_id']);

            // Kiểm tra có phải xe HPL thuê không
            $isHplRental = $vehicle->is_car_rental;
            $driverId = null;
            
            // Chỉ bắt buộc chọn tài xế nếu không phải xe HPL thuê
            if (!$isHplRental) {
                if (empty($validated['driver_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng chọn tài xế cho xe này.'
                    ], 422);
                }
                $driverId = $validated['driver_id'];
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
                'is_car_rental' => true,
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
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes']
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo nhật ký xe thành công',
                'shipment' => $shipment,
                'is_hpl_rental' => $isHplRental
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle log creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
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
            $shipment = \App\Models\Shipment::with('tollFees')->findOrFail($shipmentId);
            return response()->json([
                'success' => true,
                'log' => [
                    'id' => $shipment->id,
                    'vehicle_id' => $shipment->vehicle_id,
                    'driver_id' => $shipment->driver_id,
                    'run_date' => $shipment->run_date,
                    'start_time' => $shipment->start_time,
                    'end_time' => $shipment->end_time,
                    'start_location' => $shipment->origin,
                    'end_location' => $shipment->destination,
                    'start_odometer' => number_format($shipment->start_odometer),
                    'end_odometer' => number_format($shipment->end_odometer),
                    'overtime_rate' => number_format($shipment->overtime_rate),
                    'parking_fee' => number_format($shipment->parking_fee),
                    'notes' => $shipment->notes,
                    'toll_fees' => $shipment->tollFees->map(function($fee) {
                        return [
                            'station_name' => $fee->station_name,
                            'transaction_code' => $fee->transaction_code,
                            'fee_amount' => $fee->fee_amount,
                            'notes' => $fee->notes
                        ];
                    })->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get shipment for editing', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải dữ liệu nhật ký xe'
            ], 500);
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
            $data['start_odometer'] = str_replace(',', '', $data['start_odometer']);
            $data['end_odometer'] = str_replace(',', '', $data['end_odometer']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);
            $data['max_distance'] = str_replace(',', '', $data['max_distance'] ?? '');
            $data['over_distance_fee_per_km'] = str_replace(',', '', $data['over_distance_fee_per_km'] ?? '');
            if (!empty($data['toll_fees'])) {
                foreach ($data['toll_fees'] as &$tollFee) {
                    if (!empty($tollFee['fee_amount'])) {
                        $tollFee['fee_amount'] = str_replace(',', '', $tollFee['fee_amount']);
                    }
                }
            }
            
            $validated = validator($data, [
                'car_rental_id' => 'required|exists:car_rentals,id',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'driver_id' => 'nullable|exists:users,id',
                'run_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_location' => 'nullable|string|max:255',
                'end_location' => 'nullable|string|max:255',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'toll_fees' => 'nullable|array',
                'toll_fees.*.station_name' => 'nullable|string|max:255',
                'toll_fees.*.transaction_code' => 'nullable|string|max:255',
                'toll_fees.*.fee_amount' => 'nullable|numeric|min:0',
                'toll_fees.*.notes' => 'nullable|string'
            ])->validate();
            
            $validated['overtime_rate'] = $validated['overtime_rate'] ?? 50000;
            $validated['parking_fee'] = isset($validated['parking_fee']) ? abs((float)$validated['parking_fee']) : 0;

            // Get vehicle và car rental để lấy thông tin
            $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);
            $carRental = \App\Models\CarRental::with('customer')->findOrFail($validated['car_rental_id']);

            // Kiểm tra có phải xe HPL thuê không
            $isHplRental = $vehicle->is_car_rental;
            $driverId = null;
            
            // Chỉ bắt buộc chọn tài xế nếu không phải xe HPL thuê
            if (!$isHplRental) {
                if (empty($validated['driver_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng chọn tài xế cho xe này.'
                    ], 422);
                }
                $driverId = $validated['driver_id'];
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
            }
            $totalOvertimeCost = abs($overtimeRate * $overtimeHours);

            DB::beginTransaction();

            // Update Shipment
            $shipmentData = [
                'origin' => $validated['start_location'] ?? $shipment->origin,
                'destination' => $validated['end_location'] ?? $shipment->destination,
                'departure_time' => $startDateTime,
                'estimated_arrival_time' => $endDateTime,
                'driver_id' => $driverId,
                'vehicle_id' => $validated['vehicle_id'],
                'distance' => $totalDistance,
                
                // Thông tin từ vehicle log
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'run_date' => $validated['run_date'],
                'overtime_hours' => $overtimeHours,
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes']
            ];
            $shipment->update($shipmentData);

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

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhật ký xe thành công',
                'shipment' => $shipment,
                'is_hpl_rental' => $isHplRental
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
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
            ->where('is_car_rental', true)
            ->with(['driver', 'vehicle', 'tollFees'])
            ->orderBy('run_date', 'asc')
            ->get();

        // Group toll fees by run_date for easy access
        $tollFeesByDate = collect();
        foreach ($shipments as $shipment) {
            $dateKey = \Carbon\Carbon::parse($shipment->run_date)->format('Y-m-d');
            if (!$tollFeesByDate->has($dateKey)) {
                $tollFeesByDate->put($dateKey, collect());
            }
            $tollFeesByDate->get($dateKey)->push(...$shipment->tollFees);
        }

        $month = now()->format('m/Y');
        $fileName = 'bien_ban_nhat_ky_lo_trinh_xe_' . $carRental->id . '_' . str_replace('/', '', $month) . '.xlsx';

        return Excel::download(new \App\Exports\ShipmentVehicleLogExport($carRental, $shipments, $tollFeesByDate, $month), $fileName);
    }
}

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
use App\Models\CarRentalVehicleLog;
use App\Models\TollStation;
use App\Models\TollFee;
use Maatwebsite\Excel\Facades\Excel;

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
            // Log the request data for debugging
            $this->carRentalService->create($request->all());
            DB::commit();
            return response()->json(['message' => 'Tạo thông tin thuê xe thành công'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
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
        $vehicles = $this->vehicleRepository->all()->pluck('name', 'id');
        $carRentalstatuses = CarRental::getStatuses();
        $carRentalVehicles = $carRental->carRentalVehicles;
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');

        // Thêm danh sách nhật ký xe
        $carRentalVehicleLogs = CarRentalVehicleLog::where('car_rental_id', $id)
            ->with(['vehicle', 'driver'])
            ->latest()
            ->get();

        return view('admin.car_rental.edit', compact(
            'carRental',
            'customers',
            'carRentalVehicles',
            'vehicleTypes',
            'carRentalstatuses',
            'carRentalVehicleLogs'
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

            $validated['overtime_rate'] = 50000;
            $validated['parking_fee'] = isset($validated['parking_fee']) ? abs((float)$validated['parking_fee']) : 0;

            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);
            // Ghép run_date + start_time, end_time thành datetime cho tính toán
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

            $logData = [
                'car_rental_id' => $validated['car_rental_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'run_date' => $validated['run_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'start_location' => $validated['start_location'] ?? null,
                'end_location' => $validated['end_location'] ?? null,
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'total_distance' => $totalDistance,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'status' => CarRentalVehicleLog::STATUS_COMPLETED
            ];

            $CarRentalVehicleLog = CarRentalVehicleLog::create($logData);

            // Create toll fees if provided
            if (!empty($validated['toll_fees'])) {
                foreach ($validated['toll_fees'] as $tollFeeData) {
                    if (!empty($tollFeeData['station_name']) && !empty($tollFeeData['fee_amount'])) {
                        \App\Models\TollFee::create([
                            'vehicle_log_id' => $CarRentalVehicleLog->id,
                            'station_name' => $tollFeeData['station_name'],
                            'transaction_code' => $tollFeeData['transaction_code'] ?? null,
                            'fee_amount' => abs((float)$tollFeeData['fee_amount']),
                            'notes' => $tollFeeData['notes'] ?? null
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tạo nhật ký xe thành công',
                'log' => $CarRentalVehicleLog
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Vehicle log creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vehicle log data for editing
     * 
     * @param int $logId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editCarRentalVehicleLog($logId)
    {
        try {
            $log = CarRentalVehicleLog::with('tollFees')->findOrFail($logId);
            return response()->json([
                'success' => true,
                'log' => [
                    'id' => $log->id,
                    'vehicle_id' => $log->vehicle_id,
                    'run_date' => $log->run_date,
                    'start_time' => $log->start_time,
                    'end_time' => $log->end_time,
                    'start_location' => $log->start_location,
                    'end_location' => $log->end_location,
                    'start_odometer' => number_format($log->start_odometer),
                    'end_odometer' => number_format($log->end_odometer),
                    'overtime_rate' => number_format($log->overtime_rate),
                    'parking_fee' => number_format($log->parking_fee),
                    'notes' => $log->notes,
                    'toll_fees' => $log->tollFees->map(function($fee) {
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
            Log::error('Failed to get vehicle log for editing', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải dữ liệu nhật ký xe'
            ], 500);
        }
    }

    /**
     * Update vehicle log
     * 
     * @param Request $request
     * @param int $logId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCarRentalVehicleLog(Request $request, $logId)
    {
        try {
            $log = CarRentalVehicleLog::findOrFail($logId);
            $data = $request->all();
            $data['start_odometer'] = str_replace(',', '', $data['start_odometer']);
            $data['end_odometer'] = str_replace(',', '', $data['end_odometer']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);
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
                'run_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_location' => 'nullable|string|max:255',
                'end_location' => 'nullable|string|max:255',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'toll_fees' => 'nullable|array',
                'toll_fees.*.station_name' => 'nullable|string|max:255',
                'toll_fees.*.transaction_code' => 'nullable|string|max:255',
                'toll_fees.*.fee_amount' => 'nullable|numeric|min:0',
                'toll_fees.*.notes' => 'nullable|string'
            ])->validate();
            $validated['overtime_rate'] = 50000;
            $validated['parking_fee'] = isset($validated['parking_fee']) ? abs((float)$validated['parking_fee']) : 0;
            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);
            $startDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['start_time']);
            $endDateTime = \Carbon\Carbon::parse($validated['run_date'] . ' ' . $validated['end_time']);
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
            $logData = [
                'car_rental_id' => $validated['car_rental_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'run_date' => $validated['run_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'start_location' => $validated['start_location'] ?? null,
                'end_location' => $validated['end_location'] ?? null,
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'total_distance' => $totalDistance,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'status' => CarRentalVehicleLog::STATUS_COMPLETED
            ];
            $log->update($logData);
            // Xóa toàn bộ toll_fees cũ và lưu lại danh sách mới
            $log->tollFees()->delete();
            if (!empty($validated['toll_fees'])) {
                foreach ($validated['toll_fees'] as $tollFeeData) {
                    if (!empty($tollFeeData['station_name']) && !empty($tollFeeData['fee_amount'])) {
                        \App\Models\TollFee::create([
                            'vehicle_log_id' => $log->id,
                            'station_name' => $tollFeeData['station_name'],
                            'transaction_code' => $tollFeeData['transaction_code'] ?? null,
                            'fee_amount' => abs((float)$tollFeeData['fee_amount']),
                            'notes' => $tollFeeData['notes'] ?? null
                        ]);
                    }
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhật ký xe thành công',
                'log' => $log
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Vehicle log update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa vehicle log
     */
    public function destroyCarRentalVehicleLog($logId)
    {
        try {
            $log = CarRentalVehicleLog::findOrFail($logId);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa nhật ký xe thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete vehicle log failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhật ký xe!'
            ], 500);
        }
    }

    /**
     * Download all vehicle logs for a car rental as an Excel file
     *
     * @param int $car_rental_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadVehicleLog($car_rental_id)
    {
        $carRental = CarRental::with('customer')->findOrFail($car_rental_id);
        $logs = CarRentalVehicleLog::where('car_rental_id', $car_rental_id)
            ->with('tollFees')
            ->orderBy('run_date', 'asc')
            ->get();

        $month = now()->format('m/Y');
        $fileName = 'bien_ban_nhat_ky_lo_trinh_xe_' . $carRental->id . '_' . str_replace('/', '', $month) . '.xlsx';

        return Excel::download(new \App\Exports\VehicleLogExport($carRental, $logs, $month), $fileName);
    }
}

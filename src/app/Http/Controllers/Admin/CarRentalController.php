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
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
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
            $data['overtime_rate'] = str_replace(',', '', $data['overtime_rate']);
            $data['toll_fee'] = str_replace(',', '', $data['toll_fee']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);

            // Validate request data
            $validated = validator($data, [
                'car_rental_id' => 'required|exists:car_rentals,id',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'toll_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string'
            ])->validate();

            // Ensure all numeric values are positive
            $validated['overtime_rate'] = abs($validated['overtime_rate'] ?? 0);
            $validated['toll_fee'] = abs($validated['toll_fee'] ?? 0);
            $validated['parking_fee'] = abs($validated['parking_fee'] ?? 0);

            // Calculate additional fields
            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);
            
            // Calculate overtime hours (total time used) - ensure positive values
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $overtimeHours = abs($endTime->diffInHours($startTime));
            $totalOvertimeCost = abs(($validated['overtime_rate'] ?? 0) * $overtimeHours);

            // Ensure all calculated values are positive
            $logData = [
                'car_rental_id' => $validated['car_rental_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'total_distance' => $totalDistance,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'toll_fee' => $validated['toll_fee'],
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'status' => CarRentalVehicleLog::STATUS_COMPLETED
            ];

            // Log the data for debugging
            Log::info('Creating vehicle log with data:', $logData);

            // Create vehicle log
            $CarRentalVehicleLog = CarRentalVehicleLog::create($logData);

            // Return success response with created log
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
            $log = CarRentalVehicleLog::findOrFail($logId);
            
            return response()->json([
                'success' => true,
                'log' => [
                    'id' => $log->id,
                    'vehicle_id' => $log->vehicle_id,
                    'start_time' => $log->start_time->format('Y-m-d H:i'),
                    'end_time' => $log->end_time->format('Y-m-d H:i'),
                    'start_odometer' => number_format($log->start_odometer),
                    'end_odometer' => number_format($log->end_odometer),
                    'overtime_rate' => number_format($log->overtime_rate),
                    'toll_fee' => number_format($log->toll_fee),
                    'parking_fee' => number_format($log->parking_fee),
                    'notes' => $log->notes
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
            // Find the log
            $log = CarRentalVehicleLog::findOrFail($logId);
            
            // Clean formatted numbers before validation
            $data = $request->all();
            $data['start_odometer'] = str_replace(',', '', $data['start_odometer']);
            $data['end_odometer'] = str_replace(',', '', $data['end_odometer']);
            $data['overtime_rate'] = str_replace(',', '', $data['overtime_rate']);
            $data['toll_fee'] = str_replace(',', '', $data['toll_fee']);
            $data['parking_fee'] = str_replace(',', '', $data['parking_fee']);

            // Validate request data
            $validated = validator($data, [
                'car_rental_id' => 'required|exists:car_rentals,id',
                'vehicle_id' => 'required|exists:vehicles,vehicle_id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'start_odometer' => 'required|numeric|min:0',
                'end_odometer' => 'required|numeric|gt:start_odometer',
                'overtime_rate' => 'nullable|numeric|min:0',
                'toll_fee' => 'nullable|numeric|min:0',
                'parking_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string'
            ])->validate();

            // Ensure all numeric values are positive
            $validated['overtime_rate'] = abs($validated['overtime_rate'] ?? 0);
            $validated['toll_fee'] = abs($validated['toll_fee'] ?? 0);
            $validated['parking_fee'] = abs($validated['parking_fee'] ?? 0);

            // Calculate additional fields
            $totalDistance = abs($validated['end_odometer'] - $validated['start_odometer']);
            
            // Calculate overtime hours (total time used) - ensure positive values
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $overtimeHours = abs($endTime->diffInHours($startTime));
            $totalOvertimeCost = abs(($validated['overtime_rate'] ?? 0) * $overtimeHours);

            // Ensure all calculated values are positive
            $logData = [
                'car_rental_id' => $validated['car_rental_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'start_odometer' => abs($validated['start_odometer']),
                'end_odometer' => abs($validated['end_odometer']),
                'total_distance' => $totalDistance,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $validated['overtime_rate'],
                'total_overtime_cost' => $totalOvertimeCost,
                'toll_fee' => $validated['toll_fee'],
                'parking_fee' => $validated['parking_fee'],
                'notes' => $validated['notes'],
                'status' => CarRentalVehicleLog::STATUS_COMPLETED
            ];

            // Log the data for debugging
            Log::info('Updating vehicle log with data:', $logData);

            // Update vehicle log
            $log->update($logData);

            // Return success response with updated log
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
            \Log::error('Delete vehicle log failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhật ký xe!'
            ], 500);
        }
    }
}

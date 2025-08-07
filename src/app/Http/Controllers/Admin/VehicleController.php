<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Services\VehicleService;
use App\Models\VehicleType;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interface\VehicleRepositoryInterface as VehicleRepository;
use App\Repositories\Interface\UserRepositoryInterface as UserRepository;
use App\Models\User;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Enum\UserStatus;
use App\Models\Customer;

class VehicleController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\VehicleService $vehicleService
     * @param \App\Repositories\Interface\VehicleRepositoryInterface $vehicleRepository
     * @param \App\Repositories\Interface\UserRepositoryInterface $userRepository
     */
    public function __construct(
        protected VehicleService $vehicleService,
        protected VehicleRepository $vehicleRepository,
        protected UserRepository $userRepository
    ) {}

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $filters = $request->only(['vehicle_type_id', 'status', 'keyword', 'is_car_rental']);

        $vehicles = $this->vehicleService->getFilteredVehicles($filters);
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');
        $vehicleStatuses = Vehicle::getStatuses();
        $drivers = $this->userRepository->getUserByConditions([
            'role' => User::ROLE_DRIVER,
            'status' => UserStatus::ACTIVE
        ])->pluck('full_name', 'id');
        
        // Get car rental customers for modal
        $carRentalCustomers = \App\Models\Customer::where('type', Customer::TYPE_CARRENTAL)
            ->where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.vehicles.index', compact('vehicles', 'vehicleTypes', 'vehicleStatuses', 'drivers', 'carRentalCustomers'));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');
        $vehicleStatuses = Vehicle::getStatuses();
        $drivers = $this->userRepository->getUserByConditions([
            'role' => User::ROLE_DRIVER,
            'status' => UserStatus::ACTIVE
        ])->pluck('full_name', 'id');
        
        // Get car rental customers
        $carRentalCustomers = \App\Models\Customer::where('type', Customer::TYPE_CARRENTAL)
            ->where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.vehicles.create', compact('vehicleTypes', 'vehicleStatuses', 'drivers', 'carRentalCustomers'));
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Vehicle\StoreVehicleRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreVehicleRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->vehicleService->store($request);

            DB::commit();

            return response()->json(['message' => 'Vehicle created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // return redirect()->route('admin.vehicles.index')->with('error', 'Trang không hợp lệ.');
        $vehicle = Vehicle::with(['vehicleType', 'maintenanceRecords'])->findOrFail($id);
    
        if (request()->ajax()) {
            return view('admin.vehicles.partials.vehicle_detail', compact('vehicle'))->render();
        }
        
        return abort(404);
    }

    /**
     * Summary of edit
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Vehicle $vehicle)
    {
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');
        $vehicleStatuses = Vehicle::getStatuses();
        $drivers = $this->userRepository->getUserByConditions([
            'role' => User::ROLE_DRIVER,
            'status' => UserStatus::ACTIVE
        ])->pluck('full_name', 'id');
        
        // Get car rental customers
        $carRentalCustomers = \App\Models\Customer::where('type', Customer::TYPE_CARRENTAL)
            ->where('is_active', true)
            ->pluck('name', 'id');

        return view('admin.vehicles.edit', compact('vehicle', 'vehicleTypes', 'vehicleStatuses', 'drivers', 'carRentalCustomers'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        DB::beginTransaction();
        try {
            // Log the request data for debugging
            Log::info('Vehicle update request', [
                'request_data' => $request->all(),
                'vehicle_id' => $vehicle->vehicle_id
            ]);
            
            $this->vehicleService->update($request, $vehicle);

            DB::commit();
            
            return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vehicle update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vehicle $vehicle)
    {
        try {
            $vehicle->delete();
            return back()->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getByCarRental(Request $request)
    {
        try {
            $isRental = $request->boolean('is_car_rental');
            
            $vehicles = Vehicle::query()
                ->where('is_car_rental', $isRental)
                ->where('status', 'active') // Only active vehicles
                ->with(['vehicleType', 'driver'])
                ->orderBy('plate_number')
                ->get()
                ->map(function ($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'plate_number' => $vehicle->plate_number,
                        'vehicle_type' => $vehicle->vehicleType->name ?? '',
                        'capacity' => $vehicle->capacity,
                        'driver_name' => $vehicle->driver->name ?? 'Chưa phân công',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'vehicles' => $vehicles,
                'count' => $vehicles->count(),
                'message' => $vehicles->isEmpty() ? 'Không có phương tiện phù hợp' : ''
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getByRentalStatus: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}

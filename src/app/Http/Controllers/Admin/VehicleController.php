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
        $filters = $request->only(['vehicle_type_id', 'status', 'keyword']);

        $vehicles = $this->vehicleService->getFilteredVehicles($filters);
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');
        $vehicleStatuses = Vehicle::getStatuses();
        $drivers = $this->userRepository->getUserByConditions([
            'role' => User::ROLE_DRIVER
        ])->pluck('full_name', 'id');

        return view('admin.vehicles.index', compact('vehicles', 'vehicleTypes', 'vehicleStatuses', 'drivers'));
    }

    /**
     * Summary of create
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin.vehicles.create');
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
            'role' => User::ROLE_DRIVER
        ])->pluck('full_name', 'id');

        return view('admin.vehicles.edit', compact('vehicle', 'vehicleTypes', 'vehicleStatuses', 'drivers'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        DB::beginTransaction();
        try {
            $this->vehicleService->update($request, $vehicle);

            DB::commit();
            
            return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
}

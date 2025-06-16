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

    public function edit(CarRental $carRental)
    {
        $customers = $this->customerRepository->all()->pluck('name', 'id');
        $vehicles = $this->vehicleRepository->all()->pluck('name', 'id');
        $carRentalstatuses = CarRental::getStatuses();
        $carRentalVehicles = $carRental->carRentalVehicles;
        $vehicleTypes = VehicleType::pluck('name', 'vehicle_type_id');

        return view('admin.car_rental.edit', compact(['carRental', 'customers', 'carRentalstatuses', 'carRentalVehicles', 'vehicleTypes']));
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
}

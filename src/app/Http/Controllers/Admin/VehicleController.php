<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Services\VehicleService;

class VehicleController extends Controller
{
    /**
     * Summary of __construct
     * @param \App\Services\VehicleService $vehicleService
     */
    public function __construct(protected VehicleService $vehicleService) {}

    public function index(Request $request)
    {
        $vehicles = Vehicle::with('driver', 'vehicleType', 'documents')->latest()->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        try {
            return redirect()->route('admin.vehicles.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Vehicle $vehicle)
    {
        return view('admin.vehicles.show');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('user'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        try {
            return redirect()->route('admin.vehicles.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        return back()->with('success', 'User deleted successfully.');
    }
}

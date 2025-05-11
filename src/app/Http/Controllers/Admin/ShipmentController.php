<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.shipments.index');
    }

    public function create()
    {
        return view('admin.shipments.create');
    }

    public function store(Request $request)
    {
        try {
            return redirect()->route('admin.shipments.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Shipment $shipment)
    {
        return view('admin.shipments.show');
    }

    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('user'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        try {
            return redirect()->route('admin.shipments.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(Shipment $shipment)
    {
        return back()->with('success', 'User deleted successfully.');
    }
}

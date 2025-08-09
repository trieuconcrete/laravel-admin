<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShipmentDeductionType;
use Illuminate\Http\Request;
use App\Http\Requests\ShipmentDeductionTypeRequest;
use App\Http\Controllers\Controller;

class ShipmentDeductionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ShipmentDeductionType::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['id', 'name', 'type', 'status', 'order', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->ordered();
        }

        $deductionTypes = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $deductionTypes,
            ]);
        }

        return view('admin.shipment_deduction_types.index', compact('deductionTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $typeOptions = ShipmentDeductionType::getTypeOptions();
        $statusOptions = ShipmentDeductionType::getStatusOptions();
        
        return view('admin.shipment_deduction_types.create', compact('typeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShipmentDeductionTypeRequest $request)
    {
        $deductionType = ShipmentDeductionType::create($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Deduction type created successfully.',
                'data' => $deductionType,
            ], 201);
        }

        return redirect()->route('shipment-deduction-types.index')
            ->with('success', 'Deduction type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ShipmentDeductionType $shipmentDeductionType)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $shipmentDeductionType,
            ]);
        }

        return view('admin.shipment_deduction_types.show', compact('shipmentDeductionType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShipmentDeductionType $shipmentDeductionType)
    {
        $typeOptions = ShipmentDeductionType::getTypeOptions();
        $statusOptions = ShipmentDeductionType::getStatusOptions();
        
        return view('admin.shipment_deduction_types.edit', compact('shipmentDeductionType', 'typeOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShipmentDeductionTypeRequest $request, ShipmentDeductionType $shipmentDeductionType)
    {
        $shipmentDeductionType->update($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Deduction type updated successfully.',
                'data' => $shipmentDeductionType->fresh(),
            ]);
        }

        return redirect()->route('shipment-deduction-types.index')
            ->with('success', 'Deduction type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShipmentDeductionType $shipmentDeductionType)
    {
        $shipmentDeductionType->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Deduction type deleted successfully.',
            ]);
        }

        return redirect()->route('shipment-deduction-types.index')
            ->with('success', 'Deduction type deleted successfully.');
    }

    /**
     * Update the order of deduction types
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:shipment_deduction_types,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            ShipmentDeductionType::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
        ]);
    }

    /**
     * Bulk delete deduction types
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:shipment_deduction_types,id',
        ]);

        ShipmentDeductionType::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected deduction types deleted successfully.',
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:shipment_deduction_types,id',
            'status' => 'required|in:active,inactive',
        ]);

        ShipmentDeductionType::whereIn('id', $request->ids)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }
}
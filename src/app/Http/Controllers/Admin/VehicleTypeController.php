<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VehicleType::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'vehicle_type_id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['vehicle_type_id', 'name', 'status', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('vehicle_type_id', 'asc');
        }

        $vehicleTypes = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $vehicleTypes,
            ]);
        }

        return view('admin.vehicle-types.index', compact('vehicleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:vehicle_types,name',
                'description' => 'nullable|string|max:1000',
                'status' => 'boolean'
            ], [
                'name.required' => 'Tên loại xe là bắt buộc',
                'name.unique' => 'Tên loại xe đã tồn tại',
                'name.max' => 'Tên loại xe không được vượt quá 255 ký tự',
                'description.max' => 'Mô tả không được vượt quá 1000 ký tự'
            ]);

            $vehicleType = VehicleType::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? $request->status : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm loại xe thành công',
                'data' => $vehicleType
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm loại xe'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $vehicleType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy loại xe'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:vehicle_types,name,' . $id . ',vehicle_type_id',
                'description' => 'nullable|string|max:1000',
                'status' => 'boolean'
            ], [
                'name.required' => 'Tên loại xe là bắt buộc',
                'name.unique' => 'Tên loại xe đã tồn tại',
                'name.max' => 'Tên loại xe không được vượt quá 255 ký tự',
                'description.max' => 'Mô tả không được vượt quá 1000 ký tự'
            ]);

            $vehicleType->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->has('status') ? $request->status : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật loại xe thành công',
                'data' => $vehicleType
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật loại xe'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);

            // Kiểm tra xem có xe nào đang sử dụng loại xe này không
            if ($vehicleType->vehicles()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa loại xe này vì đang có xe sử dụng'
                ], 400);
            }

            $vehicleType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa loại xe thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa loại xe'
            ], 500);
        }
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus(string $id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);
            $vehicleType->update(['status' => !$vehicleType->status]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $vehicleType
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling vehicle type status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ], 500);
        }
    }

    /**
     * Get vehicle types for dropdown/select
     */
    public function getForSelect(Request $request)
    {
        try {
            $query = VehicleType::where('status', 1)->orderBy('name');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $vehicleTypes = $query->get(['vehicle_type_id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $vehicleTypes
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting vehicle types for select: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải danh sách loại xe'
            ], 500);
        }
    }
}

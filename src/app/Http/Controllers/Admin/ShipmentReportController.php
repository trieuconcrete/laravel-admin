<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShipmentReport\SummarizeReportRequest;
use App\Services\ShipmentReportService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ShipmentReportController extends Controller
{
    protected $shipmentReportService;

    public function __construct(ShipmentReportService $shipmentReportService)
    {
        $this->shipmentReportService = $shipmentReportService;
    }

    /**
     * Tổng kết bảng kê
     */
    public function summarize(SummarizeReportRequest $request, Customer $customer): JsonResponse
    {
        $validated = $request->validated();
        
        $result = $this->shipmentReportService->summarizeReport(
            $customer->id,
            $validated['statement_start_date'],
            $validated['statement_end_date'],
            $validated['shipment_type']
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Tổng kết bảng kê thành công',
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * Xuất Excel bảng kê
     */
    public function export(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'statement_start_date' => 'required|date',
                'statement_end_date' => 'required|date|after_or_equal:statement_start_date',
                'shipment_type' => 'required|integer|in:1,2,3,4',
            ]);

            $startDate = $request->input('statement_start_date');
            $endDate = $request->input('statement_end_date');
            $shipmentType = $request->input('shipment_type');

            $result = $this->shipmentReportService->exportReport(
                $customer->id,
                $startDate,
                $endDate,
                $shipmentType
            );

            // Nếu result là response object (từ Excel::download), trả về trực tiếp
            if (method_exists($result, 'getStatusCode')) {
                return $result;
            }

            // Nếu result là array (có lỗi), trả về JSON response
            if (is_array($result)) {
                return response()->json($result, 400);
            }

            // Fallback
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất Excel'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error exporting report: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy dữ liệu bảng kê theo thời gian và loại chuyến xe
     */
    public function getReportData(Request $request, Customer $customer): JsonResponse
    {
        $request->validate([
            'statement_start_date' => 'required|date',
            'statement_end_date' => 'required|date|after_or_equal:statement_start_date',
            'shipment_type' => 'nullable|integer|in:1,2,3,4',
        ]);

        $shipmentType = $request->shipment_type;

        $query = \App\Models\Shipment::where('customer_id', $customer->id)
            ->whereBetween('departure_time', [$request->statement_start_date, $request->statement_end_date])
            ->where('status', 'completed');

        // Nếu có shipment_type thì filter theo loại, nếu không thì lấy tất cả
        if ($shipmentType) {
            $query->where('shipment_type', $shipmentType);
        }

        $shipments = $query->get()
            ->map(function ($shipment) use ($shipmentType) {
                // Nếu không có shipment_type, sử dụng loại từ database
                $currentShipmentType = $shipmentType ?: $shipment->shipment_type;
                
                return [
                    'id' => $shipment->id,
                    'shipment_code' => $shipment->shipment_code,
                    'departure_time' => $shipment->departure_time->format('d/m/Y'),
                    'origin' => $shipment->origin,
                    'destination' => $shipment->destination,
                    'trip_count' => $shipment->trip_count ?? 1,
                    'distance' => $shipment->distance ?? 0,
                    'unit_price' => $shipment->unit_price ?? 0,
                    'crane_price' => $shipment->crane_price ?? 0,
                    'cargo_weight' => $shipment->cargo_weight ?? 0,
                    'combined_fees' => $shipment->shipmentExtraFee->sum('amount'),
                    'total_amount' => $this->calculateTotalAmount($shipment, $currentShipmentType),
                    'notes' => $shipment->notes,
                    'status' => $shipment->status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $shipments,
            'total_count' => $shipments->count(),
            'total_amount' => $shipments->sum('total_amount'),
        ]);
    }

    /**
     * Tính tổng tiền theo loại chuyến xe
     */
    private function calculateTotalAmount($shipment, $shipmentType)
    {
        switch ($shipmentType) {
            case 3: // Xe nâng
                return ($shipment->crane_price ?? 0) * ($shipment->trip_count ?? 1);
            case 4: // Xe đường dài
                return ($shipment->unit_price ?? 0) * ($shipment->distance ?? 0);
            default: // Các loại khác
                return ($shipment->unit_price ?? 0) * ($shipment->trip_count ?? 1);
        }
    }
} 
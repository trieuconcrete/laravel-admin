<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentReport;
use App\Models\Customer;
use App\Exports\PerTripShipmentExport;
use App\Exports\MonthlyRentalShipmentExport;
use App\Exports\CraneShipmentExport;
use App\Exports\LongDistanceShipmentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentReportService
{
    /**
     * Tổng kết bảng kê theo thời gian và loại chuyến xe
     */
    public function summarizeReport($customerId, $startDate, $endDate, $shipmentType)
    {
        try {
            DB::beginTransaction();

            $monthly = date('Y-m', strtotime($startDate));
            $reports = [];
            $totalAmount = 0;
            $totalShipmentCount = 0;

            // Tổng kết cho một loại cụ thể (shipment_type luôn có giá trị)
                // Tổng kết cho một loại cụ thể
                $shipments = Shipment::where('customer_id', $customerId)
                    ->where('shipment_type', $shipmentType)
                    ->whereBetween('departure_time', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->get();

                $totalAmount = $shipments->sum(function ($shipment) use ($shipmentType) {
                    return $this->calculateAmount($shipment, $shipmentType);
                });
                $vatAmount = $totalAmount * 0.08; // Assuming 8% VAT

                // Kiểm tra xem đã có báo cáo cho loại này với khoảng thời gian này chưa
                $existingReport = ShipmentReport::where('customer_id', $customerId)
                    ->where('shipment_type', $shipmentType)
                    ->where('statement_start_date', $startDate)
                    ->where('statement_end_date', $endDate)
                    ->first();

                if ($existingReport) {
                    // Cập nhật báo cáo hiện có
                    $report = $existingReport;
                    $report->update([
                        'total_amount' => $totalAmount + $vatAmount,
                        'is_finalized' => true,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    // Tạo báo cáo mới
                    $report = ShipmentReport::create([
                        'customer_id' => $customerId,
                        'monthly' => $monthly,
                        'shipment_type' => $shipmentType,
                        'total_amount' => $totalAmount + $vatAmount,
                        'statement_start_date' => $startDate,
                        'statement_end_date' => $endDate,
                        'is_finalized' => true,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $reports[] = $report;
                $totalShipmentCount = $shipments->count();



            DB::commit();

            return [
                'success' => true,
                'data' => [
                    'reports' => $reports,
                    'total_amount' => $totalAmount + $vatAmount,
                    'shipment_count' => $totalShipmentCount,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'shipment_type' => $shipmentType,
                    'is_all_types' => false,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error summarizing report: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tổng kết bảng kê: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xuất Excel bảng kê theo loại chuyến xe
     */
    public function exportReport($customerId, $startDate, $endDate, $shipmentType)
    {
        try {
            // Lấy dữ liệu shipments
            $shipments = Shipment::where('customer_id', $customerId)
                ->where('shipment_type', $shipmentType)
                ->whereBetween('departure_time', [$startDate, $endDate])
                ->where('status', 'completed')
                ->with(['customer', 'vehicle', 'vehicle.vehicleType', 'driver'])
                ->get()
                ->map(function ($shipment) use ($shipmentType) {
                    return [
                        'id' => $shipment->id,
                        'shipment_code' => $shipment->shipment_code,
                        'vehicle_plate_number' => $shipment->vehicle ? $shipment->vehicle->plate_number : '',
                        'departure_time' => $shipment->departure_time->format('d/m/Y'),
                        'origin' => $shipment->origin,
                        'destination' => $shipment->destination,
                        'trip_count' => $shipment->trip_count ?? 1,
                        'distance' => $shipment->distance ?? 0,
                        'unit_price' => $shipment->unit_price ?? 0,
                        'crane_price' => $shipment->crane_price ?? 0,
                        'cargo_weight' => $shipment->cargo_weight ?? 0,
                        'combined_fees' => $shipment->shipmentExtraFee->sum('amount'),
                        'total_amount' => $this->calculateAmount($shipment, $shipmentType),
                        'total_expense_deductions' => $shipment->total_expense_deductions,
                        'total_combined_surcharge' => $shipment->total_combined_surcharge, // phụ thu kết hợp
                        'total_cargo_handling' => $shipment->total_cargo_handling, // bốc xếp
                        'notes' => $shipment->notes,
                        'status' => $shipment->status,
                        'plate_number' => $shipment->vehicle ? $shipment->vehicle->plate_number : '',
                    ];
                });

            // Lấy thông tin khách hàng
            $customer = Customer::find($customerId);

            if (!$customer) {
                throw new \Exception('Không tìm thấy khách hàng');
            }

            // Tạo filename
            $filename = $this->generateFilename($customer, $startDate, $endDate, $shipmentType);

            // Chọn export class dựa trên shipment type
            $exportClass = $this->getExportClass($shipmentType);

            return Excel::download(
                new $exportClass($customer, $shipments, $startDate, $endDate, $shipmentType),
                $filename
            );

        } catch (\Exception $e) {
            Log::error('Error exporting report: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy export class dựa trên shipment type
     */
    private function getExportClass($shipmentType)
    {
        switch ($shipmentType) {
            case 1:
                return PerTripShipmentExport::class; // Chuyến xe theo chuyến
            case 2:
                return MonthlyRentalShipmentExport::class; // Thuê xe theo tháng
            case 3:
                return CraneShipmentExport::class; // Xe nâng
            case 4:
                return LongDistanceShipmentExport::class; // Xe đường dài
            default:
                return PerTripShipmentExport::class; // Chuyến xe theo chuyến
        }
    }

    /**
     * Tính thành tiền theo loại chuyến xe
     */
    private function calculateAmount($shipment, $shipmentType)
    {
        switch ($shipmentType) {
            case 3: // Xe nâng
                $totalAmount = ($shipment->crane_price ?? 0) * ($shipment->trip_count ?? 1);
            case 4: // Xe đường dài
                $totalAmount = ($shipment->unit_price ?? 0) * ($shipment->distance ?? 0);
            default: // Các loại khác
                $totalAmount = ($shipment->unit_price ?? 0) * ($shipment->trip_count ?? 1);
        }
        return $totalAmount + $shipment->total_expense_deductions;
    }

    /**
     * Tạo tên file
     */
    private function generateFilename($customer, $startDate, $endDate, $shipmentType)
    {
        $customerName = str_replace(' ', '_', $customer->name);
        $startDateStr = date('Y-m-d', strtotime($startDate));
        $endDateStr = date('Y-m-d', strtotime($endDate));
        $typeNames = [1 => 'theo_chuyen', 2 => 'thue_thang', 3 => 'xe_nang', 4 => 'duong_dai'];
        
        return "Bang_ke_{$customerName}_{$typeNames[$shipmentType]}_{$startDateStr}_{$endDateStr}.xlsx";
    }
} 
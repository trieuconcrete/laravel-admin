<?php

namespace App\Exports;

use App\Models\CarRental;
use App\Models\CarRentalVehicleLog;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class VehicleLogExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $carRental;
    protected $vehicleLogs;
    protected $month;

    /**
     * @param CarRental $carRental
     * @param Collection $vehicleLogs
     * @param string $month
     */
    public function __construct(CarRental $carRental, $vehicleLogs, string $month = null)
    {
        $this->carRental = $carRental;
        $this->vehicleLogs = $vehicleLogs;
        $this->month = $month ?? now()->format('m/Y');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $monthDate = \DateTime::createFromFormat('m/Y', $this->month);
        $monthName = $monthDate->format('m/Y');
        
        return "Nhật ký lộ trình xe {$monthName}";
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Format month name
        $monthDate = \DateTime::createFromFormat('m/Y', $this->month);
        $monthName = $monthDate->format('m/Y');
        
        // Company information - Header
        $companyName = Setting::get('company_name', 'CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG');
        $companyAddress = Setting::get('company_address', 'SỐ 216, Tổ 4, Ấp 7, Xã Bình Sơn, Huyện Long Thành, Tỉnh Đồng Nai');
        $companyPhone = Setting::get('company_phone', '0917.7-12.195');
        
        // Customer information
        $customerName = $this->carRental->customer->name ?? 'CÔNG TY TNHH UZIN METAL';
        $customerTaxCode = $this->carRental->customer->tax_code ?? '3603618391';
        $customerAddress = $this->carRental->customer->address ?? 'Đường Nguyễn Ái Quốc, Khu Công Nghiệp Nhơn Trạch III-Giai đoạn 2, Thị trấn Hiệp Phước, Huyện Nhơn Trạch, Tỉnh Đồng Nai, Việt Nam';
        $customerEmail = $this->carRental->customer->email ?? null;
        
        // Set company information (Top Left)
        $sheet->setCellValue('A1', $companyName);
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress);
        $sheet->setCellValue('A3', 'ĐT liên hệ: ' . $companyPhone);
        
        // Add main title
        $vehiclePlate = $this->getVehiclePlate();
        $sheet->setCellValue('A5', 'BIÊN BẢN XÁC NHẬN NHẬT KÝ LỘ TRÌNH XE ' . $vehiclePlate);
        $sheet->mergeCells('A5:N5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add month on separate row
        $sheet->setCellValue('A6', 'THÁNG ' . $monthName);
        $sheet->mergeCells('A6:N6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set customer information (Middle Left)
        $sheet->setCellValue('A8', $customerName);
        $sheet->setCellValue('A9', 'MST: ' . $customerTaxCode);
        $sheet->setCellValue('A10', 'Địa chỉ: ' . $customerAddress);
        $sheet->setCellValue('A11', 'Email: ' . $customerEmail);
        
        // Set invoice/report details (same row as customer info)
        $sheet->setCellValue('M8', 'Hóa đơn số: ' . str_pad($this->carRental->id, 8, '0', STR_PAD_LEFT));
        $sheet->setCellValue('M9', 'Bảng kê số: ' . strtoupper(substr($customerName ?? 'KH', 0, 2)) . $monthDate->format('m/Y'));
        $sheet->setCellValue('M10', 'ĐVT: VNĐ');
        
        // Format company and customer headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10);
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A9:A11')->getFont()->setSize(10);
        $sheet->getStyle('M8:M10')->getFont()->setBold(true)->setSize(10);
        
        // Table headers - Row 12
        $headers = [
            'STT', 'Ngày Tháng', 'Lịch trình', 'Thời gian làm việc', '', 'Thời gian tăng ca', 'Đơn giá tăng ca', 
            'Tổng chi phí phát sinh tăng', 'Km bắt đầu', 'Km kết thúc', 'Số km đi trong ngày', 
            'Phụ phí phí cầu', 'Phí đậu xe', 'THỜI GIAN TĂNG CA'
        ];
        
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '12', $headers[$index]);
        }
        
        // Merge cells for working hours header
        $sheet->mergeCells('D12:E12');
        $sheet->setCellValue('D13', 'Bắt đầu');
        $sheet->setCellValue('E13', 'Kết thúc');
        
        // Style the header rows
        $sheet->getStyle('A12:N12')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFD9EAD3'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $sheet->getStyle('D13:E13')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFD9EAD3'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setAutoSize(false);
        $sheet->getColumnDimension('A')->setWidth(5);   // STT
        $sheet->getColumnDimension('B')->setWidth(12);  // Ngày Tháng
        $sheet->getColumnDimension('C')->setWidth(30);  // Lịch trình
        $sheet->getColumnDimension('D')->setWidth(10);  // Bắt đầu
        $sheet->getColumnDimension('E')->setWidth(10);  // Kết thúc
        $sheet->getColumnDimension('F')->setWidth(15);  // Thời gian tăng ca
        $sheet->getColumnDimension('G')->setWidth(15);  // Đơn giá tăng ca
        $sheet->getColumnDimension('H')->setWidth(25);  // Tổng chi phí phát sinh tăng
        $sheet->getColumnDimension('I')->setWidth(12);  // Km bắt đầu
        $sheet->getColumnDimension('J')->setWidth(12);  // Km kết thúc
        $sheet->getColumnDimension('K')->setWidth(20);  // Số km đi trong ngày
        $sheet->getColumnDimension('L')->setWidth(15);  // Phụ phí phí cầu
        $sheet->getColumnDimension('M')->setWidth(12);  // Phí đậu xe
        $sheet->getColumnDimension('N')->setWidth(20);  // THỜI GIAN TĂNG CA
        
        // Add data rows starting from row 14
        $row = 14;
        $totalOvertimeCost = 0;
        $totalDistance = 0;
        $totalTollFee = 0;
        $totalParkingFee = 0;
        $totalOvertimeHours = 0;
        
        foreach ($this->vehicleLogs as $index => $log) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, Carbon::parse($log->run_date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $log->start_location . ' -> ' . $log->end_location);
            $sheet->setCellValue('D' . $row, Carbon::parse($log->start_time)->format('H:i'));
            $sheet->setCellValue('E' . $row, Carbon::parse($log->end_time)->format('H:i'));
            $sheet->setCellValue('F' . $row, number_format($log->overtime_hours, 1));
            $sheet->setCellValue('G' . $row, number_format($log->overtime_rate));
            $sheet->setCellValue('H' . $row, $log->total_overtime_cost > 0 ? number_format($log->total_overtime_cost) : '-');
            $sheet->setCellValue('I' . $row, number_format($log->start_odometer));
            $sheet->setCellValue('J' . $row, number_format($log->end_odometer));
            $sheet->setCellValue('K' . $row, number_format($log->total_distance));
            $sheet->setCellValue('L' . $row, number_format($log->total_toll_fee));
            $sheet->setCellValue('M' . $row, number_format($log->parking_fee));
            
            // Calculate overtime time range
            $overtimeTimeRange = '';
            if ($log->overtime_hours > 0) {
                $runDate = Carbon::parse($log->run_date)->format('Y-m-d');
                $overtimeStart = Carbon::parse($runDate . ' 17:30');
                $overtimeEnd = Carbon::parse($runDate . ' ' . $log->end_time);
                $overtimeTimeRange = $overtimeStart->format('H:i') . '-' . $overtimeEnd->format('H:i');
            }
            $sheet->setCellValue('N' . $row, $overtimeTimeRange);
            
            $totalOvertimeCost += $log->total_overtime_cost;
            $totalDistance += $log->total_distance;
            $totalTollFee += $log->total_toll_fee;
            $totalParkingFee += $log->parking_fee;
            $totalOvertimeHours += $log->overtime_hours;
            $row++;
        }
        
        // Add summary row
        $summaryRow = $row;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG');
        $sheet->mergeCells('A' . $summaryRow . ':E' . $summaryRow);
        $sheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('F' . $summaryRow, number_format($totalOvertimeHours, 1));
        $sheet->setCellValue('G' . $summaryRow, '');
        $sheet->setCellValue('H' . $summaryRow, number_format($totalOvertimeCost));
        $sheet->setCellValue('I' . $summaryRow, '');
        $sheet->setCellValue('J' . $summaryRow, '');
        $sheet->setCellValue('K' . $summaryRow, number_format($totalDistance));
        $sheet->setCellValue('L' . $summaryRow, number_format($totalTollFee));
        $sheet->setCellValue('M' . $summaryRow, number_format($totalParkingFee));
        $sheet->setCellValue('N' . $summaryRow, '');
        
        // Center align all columns from F to N in summary row
        $sheet->getStyle('F' . $summaryRow . ':N' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style the data rows including summary row
        $dataRange = 'A14:N' . $summaryRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set number formats
        $sheet->getStyle('F14:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.0');
        $sheet->getStyle('G14:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I14:K' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('L14:M' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A14:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D14:E' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F14:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G14:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I14:K' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L14:M' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('N14:N' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':N' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('H' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('L' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('M' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Add itemized fees section
        $feesRow = $summaryRow + 2;
        
        // Monthly rental fee
        $monthlyRentalFee = $this->carRental->monthly_rental_fee ?? 0;
        $sheet->setCellValue('A' . $feesRow, '1/ Phí thuê xe tháng:');
        $sheet->setCellValue('E' . $feesRow, number_format($monthlyRentalFee));
        
        // Overtime fee
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, '2/ Phát sinh phí tăng ca: (' . number_format($totalOvertimeHours, 1) . ' giờ x 50.000 đồng)');
        $sheet->setCellValue('E' . $feesRow, number_format($totalOvertimeCost));
        
        // Toll fee
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, '3/ Phát sinh phụ phí cầu đường:');
        $sheet->setCellValue('E' . $feesRow, number_format($totalTollFee));
        
        // Parking fee
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, '4/ Phí bãi xe:');
        $sheet->setCellValue('E' . $feesRow, number_format($totalParkingFee));
        
        // Distance over limit fee (placeholder)
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, '5/ Phát sinh phí vượt giới hạn 2.700km:');
        $sheet->setCellValue('E' . $feesRow, '');
        
        // Calculate totals
        $subtotal = $monthlyRentalFee + $totalOvertimeCost + $totalTollFee + $totalParkingFee;
        $vatRate = 8; // 8%
        $vatAmount = $subtotal * ($vatRate / 100);
        $totalWithVat = $subtotal + $vatAmount;
        
        // Subtotal
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, 'Tổng cộng (chưa thuế VAT):');
        $sheet->setCellValue('E' . $feesRow, number_format($subtotal));
        
        // VAT
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, 'Thuế VAT ' . $vatRate . '%:');
        $sheet->setCellValue('E' . $feesRow, number_format($vatAmount));
        
        // Total with VAT
        $feesRow++;
        $sheet->setCellValue('A' . $feesRow, 'Tổng cộng bao gồm thuế VAT:');
        $sheet->setCellValue('E' . $feesRow, number_format($totalWithVat));
        
        // Style the fees section
        $feesRange = 'A' . ($summaryRow + 2) . ':H' . $feesRow;
        $sheet->getStyle($feesRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Right align the amounts
        $sheet->getStyle('H' . ($summaryRow + 2) . ':H' . $feesRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H' . ($summaryRow + 2) . ':H' . $feesRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Bold the totals
        $sheet->getStyle('A' . ($feesRow - 2) . ':H' . ($feesRow - 2))->getFont()->setBold(true);
        $sheet->getStyle('A' . $feesRow . ':H' . $feesRow)->getFont()->setBold(true);
        
        // Underline the totals
        $sheet->getStyle('H' . ($feesRow - 2))->getFont()->setUnderline(true);
        $sheet->getStyle('H' . $feesRow)->getFont()->setUnderline(true);
        
        // Add signature section
        $signatureRow = $feesRow + 3;
        $currentDate = Carbon::now()->format('d/m/Y');
        $timestamp = strtotime(str_replace('/', '-', $currentDate));
        $currentDate = 'ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
        
        // Customer confirmation (left side)
        $sheet->setCellValue('C' . $signatureRow, 'XÁC NHẬN CỦA KHÁCH HÀNG');
        $sheet->setCellValue('C' . ($signatureRow + 1), $customerName ?? 'CÔNG TY TNHH UZIN METAL');
        
        // Company and date (right side)
        $sheet->setCellValue('L' . $signatureRow, 'Long Thành, ' . $currentDate);
        $sheet->setCellValue('L' . ($signatureRow + 1), $companyName);
        
        // Style signature section
        $sheet->getStyle('C' . $signatureRow)->getFont()->setBold(true);
        $sheet->getStyle('C' . ($signatureRow + 1))->getFont()->setBold(true);
        $sheet->getStyle('L' . $signatureRow)->getFont()->setItalic(true);
        $sheet->getStyle('L' . ($signatureRow + 1))->getFont()->setBold(true);
        
        $sheet->getStyle('C' . $signatureRow . ':C' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L' . $signatureRow . ':L' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        return $sheet;
    }
    
    /**
     * Get vehicle plate number from car rental
     */
    private function getVehiclePlate()
    {
        $vehicle = $this->carRental->carRentalVehicles->first();
        if ($vehicle && $vehicle->vehicle) {
            return $vehicle->vehicle->plate_number ?? 'N/A';
        }
        return 'N/A';
    }
} 
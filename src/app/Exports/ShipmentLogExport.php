<?php

namespace App\Exports;

use App\Models\CarRental;
use App\Facades\Setting;
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

class ShipmentLogExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $carRental;
    protected $shipments;
    protected $tollFeesByDate;
    protected $month;

    /**
     * @param CarRental $carRental
     * @param Collection $shipments
     * @param Collection $tollFeesByDate
     * @param string $month
     */
    public function __construct(CarRental $carRental, $shipments, $tollFeesByDate, string $month = null)
    {
        $this->carRental = $carRental;
        $this->shipments = $shipments;
        $this->tollFeesByDate = $tollFeesByDate;
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
        $vietnameseMonth = $monthDate->format('m/Y');

        // Get company settings
        $companyName = Setting::get('company_name', 'CÔNG TY CỔ PHẦN VẬN TẢI HPL');
        $companyAddress = Setting::get('company_address', '');

        // Header content
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', $companyName);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($companyAddress) {
            $sheet->mergeCells('A2:N2');
            $sheet->setCellValue('A2', $companyAddress);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->mergeCells('A4:N4');
        $sheet->setCellValue('A4', "BIÊN BẢN NHẬT KÝ LỘ TRÌNH XE THÁNG {$vietnameseMonth}");
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Customer and rental info
        $sheet->setCellValue('A6', 'Tên khách hàng: ' . $this->carRental->customer->name);
        $sheet->mergeCells('A6:E6');
        $sheet->getStyle('A6')->getFont()->setBold(true);

        // Table headers
        $headers = [
            'A13' => 'STT',
            'B13' => 'Ngày',
            'C13' => 'Lộ trình',
            'D13' => 'Giờ bắt đầu',
            'E13' => 'Giờ kết thúc',
            'F13' => 'Số giờ tăng ca',
            'G13' => 'Đơn giá tăng ca',
            'H13' => 'Thành tiền tăng ca',
            'I13' => 'Km bắt đầu',
            'J13' => 'Km kết thúc',
            'K13' => 'Số km đi trong ngày',
            'L13' => 'Phụ phí phí cầu',
            'M13' => 'Phí đậu xe',
            'N13' => 'THỜI GIAN TĂNG CA'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Auto-size columns
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set specific widths for better display
        $sheet->getColumnDimension('C')->setWidth(30);  // Lộ trình
        $sheet->getColumnDimension('H')->setWidth(18);  // Thành tiền tăng ca
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
        
        foreach ($this->shipments as $index => $shipment) {
            // Get toll fees for this date
            $dateKey = Carbon::parse($shipment->run_date)->format('Y-m-d');
            $dayTollFees = $this->tollFeesByDate->get($dateKey, collect());
            $dayTollFeeAmount = $dayTollFees->sum('fee_amount');
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, Carbon::parse($shipment->run_date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, ($shipment->origin ?? '') . ' -> ' . ($shipment->destination ?? ''));
            // Format time safely - handle both time format and datetime
            $startTime = $shipment->start_time;
            $endTime = $shipment->end_time;
            
            // If it's a full datetime, extract time part
            if (strlen($startTime) > 8) {
                $startTime = Carbon::parse($startTime)->format('H:i');
            } else {
                // If it's already time format, just truncate to H:i
                $startTime = substr($startTime, 0, 5);
            }
            
            if (strlen($endTime) > 8) {
                $endTime = Carbon::parse($endTime)->format('H:i');
            } else {
                $endTime = substr($endTime, 0, 5);
            }
            
            $sheet->setCellValue('D' . $row, $startTime);
            $sheet->setCellValue('E' . $row, $endTime);
            $sheet->setCellValue('F' . $row, number_format($shipment->overtime_hours ?? 0, 1));
            $sheet->setCellValue('G' . $row, number_format($shipment->overtime_rate ?? 0));
            $sheet->setCellValue('H' . $row, ($shipment->total_overtime_cost ?? 0) > 0 ? number_format($shipment->total_overtime_cost) : '-');
            $sheet->setCellValue('I' . $row, number_format($shipment->start_odometer ?? 0));
            $sheet->setCellValue('J' . $row, number_format($shipment->end_odometer ?? 0));
            $sheet->setCellValue('K' . $row, number_format($shipment->actual_distance ?? 0));
            $sheet->setCellValue('L' . $row, number_format($dayTollFeeAmount));
            $sheet->setCellValue('M' . $row, number_format($shipment->parking_fee ?? 0));
            
            // Calculate overtime time range
            $overtimeTimeRange = '';
            if ($shipment->overtime_hours > 0) {
                try {
                    // Parse times safely - handle both time and datetime formats
                    $runDate = Carbon::parse($shipment->run_date)->format('Y-m-d');
                    
                    // Extract time part only from start_time and end_time
                    $startTimeOnly = $shipment->start_time;
                    $endTimeOnly = $shipment->end_time;
                    
                    // If it contains date, extract time part
                    if (strlen($startTimeOnly) > 8) {
                        $startTimeOnly = Carbon::parse($startTimeOnly)->format('H:i:s');
                    }
                    if (strlen($endTimeOnly) > 8) {
                        $endTimeOnly = Carbon::parse($endTimeOnly)->format('H:i:s');
                    }
                    
                    $startTime = Carbon::parse($runDate . ' ' . $startTimeOnly);
                    $endTime = Carbon::parse($runDate . ' ' . $endTimeOnly);
                    $overtimeStart = Carbon::parse($runDate . ' 17:30:00');
                    
                    if ($endTime->greaterThan($overtimeStart)) {
                        $effectiveStart = $startTime->greaterThan($overtimeStart) ? $startTime : $overtimeStart;
                        $overtimeTimeRange = $effectiveStart->format('H:i') . ' - ' . $endTime->format('H:i');
                    }
                } catch (\Exception $e) {
                    // If parsing fails, leave overtime range empty
                    $overtimeTimeRange = '';
                }
            }
            $sheet->setCellValue('N' . $row, $overtimeTimeRange);
            
            // Accumulate totals
            $totalOvertimeCost += $shipment->total_overtime_cost ?? 0;
            $totalDistance += $shipment->actual_distance ?? 0;
            $totalTollFee += $dayTollFeeAmount;
            $totalParkingFee += $shipment->parking_fee ?? 0;
            $totalOvertimeHours += $shipment->overtime_hours ?? 0;
            
            $row++;
        }
        
        // Add totals row
        $sheet->setCellValue('A' . $row, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('F' . $row, number_format($totalOvertimeHours, 1));
        $sheet->setCellValue('H' . $row, number_format($totalOvertimeCost));
        $sheet->setCellValue('K' . $row, number_format($totalDistance));
        $sheet->setCellValue('L' . $row, number_format($totalTollFee));
        $sheet->setCellValue('M' . $row, number_format($totalParkingFee));
        
        // Style totals row
        $sheet->getStyle('A' . $row . ':N' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':N' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        // Apply borders to data table
        $tableRange = 'A13:N' . $row;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align numeric columns
        $numericColumns = ['A', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle($col . '14:' . $col . $row)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Add cost summary section (similar to edit view)
        $summaryStartRow = $row + 3;
        
        // Section header
        $sheet->mergeCells('A' . $summaryStartRow . ':N' . $summaryStartRow);
        $sheet->setCellValue('A' . $summaryStartRow, 'CHI TIẾT PHÍ THUÊ XE');
        $sheet->getStyle('A' . $summaryStartRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $summaryStartRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $summaryStartRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE6F3FF');
        
        $summaryRow = $summaryStartRow + 2;
        
        // Monthly rental fee
        $sheet->setCellValue('A' . $summaryRow, 'Phí thuê xe tháng:');
        $sheet->setCellValue('D' . $summaryRow, number_format($this->carRental->monthly_rental_fee ?? 0, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Overtime costs
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí tăng ca (' . number_format($totalOvertimeHours, 2) . ' giờ x 50.000 VND):');
        $sheet->setCellValue('D' . $summaryRow, number_format($totalOvertimeCost, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Toll fees
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phụ phí cầu đường:');
        $sheet->setCellValue('D' . $summaryRow, number_format($totalTollFee, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Parking fees
        $sheet->setCellValue('A' . $summaryRow, 'Phí bãi xe:');
        $sheet->setCellValue('D' . $summaryRow, number_format($totalParkingFee, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Over distance fee
        $overDistanceFee = $this->carRental->over_distance_fee ?? 0;
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí vượt giới hạn km:');
        $sheet->setCellValue('D' . $summaryRow, number_format($overDistanceFee, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        if ($overDistanceFee > 0) {
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, '(' . number_format($this->carRental->total_distance ?? 0, 0, ',', '.') . ' km - ' . number_format($this->carRental->max_distance ?? 0, 0, ',', '.') . ' km) × ' . number_format($this->carRental->over_distance_fee_per_km_unit ?? 0, 0, ',', '.') . ' VNĐ/km');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setItalic(true)->setSize(10);
        }
        $summaryRow++;
        
        // Subtotal (before VAT)
        $subtotal = ($this->carRental->monthly_rental_fee ?? 0) + $totalOvertimeCost + $totalTollFee + $totalParkingFee + $overDistanceFee;
        $sheet->setCellValue('A' . $summaryRow, 'Tổng cộng (chưa thuế VAT):');
        $sheet->setCellValue('D' . $summaryRow, number_format($subtotal, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFEBCD');
        $summaryRow++;
        
        // VAT
        $vatAmount = $subtotal * 0.08;
        $sheet->setCellValue('A' . $summaryRow, 'Thuế VAT 8%:');
        $sheet->setCellValue('D' . $summaryRow, number_format($vatAmount, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('D' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Total with VAT
        $totalWithVat = $subtotal + $vatAmount;
        $sheet->setCellValue('A' . $summaryRow, 'Tổng cộng bao gồm thuế VAT:');
        $sheet->setCellValue('D' . $summaryRow, number_format($totalWithVat, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $summaryRow . ':D' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF90EE90');
        
        // Apply borders to summary section
        $summaryRange = 'A' . ($summaryStartRow + 2) . ':D' . $summaryRow;
        $sheet->getStyle($summaryRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return $sheet;
    }
} 
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

    protected $companyName;
    protected $companyAddress;
    protected $companyTaxCode;
    protected $headers;
    protected $firstColumn;
    protected $lastColumn;

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

        // Company information - Header and Footer
        $this->companyName = Setting::get('company_name', 'CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG');
        $this->companyAddress = Setting::get('company_address', 'Số 216, tổ 4, ấp 7, Bình Sơn, Long Thành, Đồng Nai');
        $this->companyTaxCode = Setting::get('company_tax_code', '3603231556');

        $this->headers = $this->getHeaders();
        $this->firstColumn = array_key_first($this->headers);
        $this->lastColumn = array_key_last($this->headers);
    }

    protected function getHeaders()
    {
        return [
            'A' => 'STT',
            'B' => 'Ngày',
            'C' => 'Lộ trình',
            'D' => 'Giờ bắt đầu',
            'E' => 'Giờ kết thúc',
            'F' => 'Số giờ tăng ca',
            'G' => 'Đơn giá tăng ca',
            'H' => 'Thành tiền tăng ca',
            'I' => 'Km bắt đầu',
            'J' => 'Km kết thúc',
            'K' => 'Số km đi trong ngày',
            'L' => 'Phụ phí phí cầu đường',
            'M' => 'Phí đậu xe',
            'N' => 'Phí cân xe',
            'O' => 'Phụ phí kiểm tra', 
            'P' => 'THỜI GIAN TĂNG CA'
        ];
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
     * Summary of setUpHeaderAndFooter
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function setUpHeaderAndFooter(Worksheet $sheet): void
    {
        // Format month name
        $monthDate = \DateTime::createFromFormat('m/Y', $this->month);
        $vietnameseMonth = $monthDate->format('m/Y');

        // Set company information
        $sheet->setCellValue('A1', $this->companyName);
        $sheet->setCellValue('A2', 'Địa chỉ: ' . $this->companyAddress);
        $sheet->setCellValue('A3', 'MST: ' . $this->companyTaxCode);
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2' . ':' . $this->lastColumn . '2');
        $sheet->mergeCells('A3:D3');
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(11);
        
        // Add report title
        $sheet->getRowDimension(5)->setRowHeight(25); // Height in points (you can adjust this value)
        $sheet->setCellValue('A5', "BIÊN BẢN NHẬT KÝ LỘ TRÌNH XE THÁNG {$vietnameseMonth}");
        $sheet->mergeCells('A5:K5');
        $sheet->mergeCells('A6:K6');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:A6')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
        
        // Customer information
        $sheet->setCellValue('A8', 'Kính gửi: ' . $this->carRental->customer->name);
        $sheet->setCellValue('A9', 'Địa chỉ: ' . $this->carRental->customer->address);
        $sheet->setCellValue('A10', 'MST: ' . $this->carRental->customer->tax_code);
        $sheet->setCellValue('A11', 'Email: ' . $this->carRental->customer->email);
        $sheet->mergeCells('A8:D8');
        $sheet->mergeCells('A9' . ':' . $this->lastColumn . '9');
        $sheet->mergeCells('A10:D10');
        $sheet->mergeCells('A11:D11');
        
        $sheet->getStyle('A8')->getFont()->setBold(true);
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $this->setUpHeaderAndFooter($sheet);

        // Table headers
        $headers = $this->getHeaders();

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell . '13', $value);
            $sheet->getStyle($cell . '13')->getFont()->setBold(true);
            $sheet->getStyle($cell . '13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell . '13')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Auto-size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set specific widths for better display
        $sheet->getColumnDimension('C')->setWidth(30);  // Lộ trình
        $sheet->getColumnDimension('H')->setWidth(18);  // Thành tiền tăng ca
        $sheet->getColumnDimension('K')->setWidth(20);  // Số km đi trong ngày
        $sheet->getColumnDimension('L')->setWidth(15);  // Phụ phí phí cầu
        $sheet->getColumnDimension('M')->setWidth(12);  // Phí đậu xe
        $sheet->getColumnDimension('N')->setWidth(12);  // Phí cân xe
        $sheet->getColumnDimension('O')->setWidth(15);  // Phụ phí kiểm tra
        $sheet->getColumnDimension('P')->setWidth(20);  // THỜI GIAN TĂNG CA
        
        // Add data rows starting from row 14
        $row = 14;
        $totalOvertimeCost = 0;
        $totalDistance = 0;
        $totalTollFees = 0;
        $totalParkingFees = 0;
        $totalTollFeesWithVAT = 0;
        $totalOvertimeHours = 0;
        $totalWeighingFee = 0;
        $totalTestingSurcharge = 0;
        
        foreach ($this->shipments as $index => $shipment) {
            // Calculate toll fees for this shipment (same as edit view)
            $shipmentTollFees = isset($shipment->tollFees) && $shipment->tollFees->count() > 0 
                ? $shipment->tollFees->sum('fee_amount') 
                : null;
            
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
            $sheet->setCellValue('L' . $row, number_format($shipmentTollFees));
            $sheet->setCellValue('M' . $row, number_format($shipment->parking_fee ?? 0));
            $sheet->setCellValue('N' . $row, number_format($shipment->weighing_fee ?? 0));
            $sheet->setCellValue('O' . $row, number_format($shipment->testing_surcharge ?? 0));

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
                    
                    // Sử dụng start_working_hour và end_working_hour từ car rental
                    $startWorkingHour = $this->carRental->start_working_hour ?? '07:00';
                    $endWorkingHour = $this->carRental->end_working_hour ?? '17:30';
                    $startWorking = Carbon::parse($runDate . ' ' . $startWorkingHour . ':00');
                    $endWorking = Carbon::parse($runDate . ' ' . $endWorkingHour . ':00');
                    
                    $morningOT = '';
                    $afternoonOT = '';
                    
                    // Tính OT buổi sáng (khi bắt đầu sớm hơn start_working_hour)
                    if ($startTime->lessThan($startWorking)) {
                        $morningHours = $startWorking->floatDiffInRealHours($startTime);
                        $morningOT = 'Sáng: ' . number_format($morningHours, 1) . 'h';
                    }
                    
                    // Tính OT buổi chiều (khi kết thúc muộn hơn end_working_hour)
                    if ($endTime->greaterThan($endWorking)) {
                        $afternoonHours = $endTime->floatDiffInRealHours($endWorking);
                        $afternoonOT = 'Chiều: ' . number_format($afternoonHours, 1) . 'h';
                    }
                    
                    // Kết hợp thông tin OT
                    if ($morningOT && $afternoonOT) {
                        $overtimeTimeRange = $morningOT . ' | ' . $afternoonOT;
                    } elseif ($morningOT) {
                        $overtimeTimeRange = $morningOT;
                    } elseif ($afternoonOT) {
                        $overtimeTimeRange = $afternoonOT;
                    }
                } catch (\Exception $e) {
                    // If parsing fails, leave overtime range empty
                    $overtimeTimeRange = '';
                }
            }
            $sheet->setCellValue('P' . $row, $overtimeTimeRange);
            
            // Accumulate totals (using same logic as edit view)
            $totalOvertimeCost += $shipment->total_overtime_cost ?? 0;
            $totalDistance += $shipment->actual_distance ?? 0;
            
            // Tính tổng phí cầu đường (same as edit view)
            if (isset($shipment->tollFees) && $shipment->tollFees->count() > 0) {
                $totalTollFees += $shipment->tollFees->sum('fee_amount');
            }
            
            // Tính tổng phí đậu xe (same as edit view)  
            $totalParkingFees += $shipment->parking_fee ?? 0;
            $totalOvertimeHours += $shipment->overtime_hours ?? 0;
            $totalTestingSurcharge += $shipment->testing_surcharge ?? 0;
            $totalWeighingFee += $shipment->weighing_fee ?? 0;
            
            $row++;
        }
        $totalTollFeesWithVAT = ($totalTollFees + $totalParkingFees) / 1.08;
        
        // Add totals row
        $sheet->setCellValue('A' . $row, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('F' . $row, number_format($totalOvertimeHours, 1));
        $sheet->setCellValue('H' . $row, number_format($totalOvertimeCost));
        $sheet->setCellValue('K' . $row, number_format($totalDistance));
        $sheet->setCellValue('L' . $row, number_format($totalTollFees));
        $sheet->setCellValue('M' . $row, number_format($totalParkingFees));
        $sheet->setCellValue('N' . $row, number_format($totalWeighingFee));
        $sheet->setCellValue('O' . $row, number_format($totalTestingSurcharge));

        // Style totals row
        $sheet->getStyle('A' . $row . ':P' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':P' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        // Apply borders to data table
        $tableRange = 'A13:P' . $row;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Center align numeric columns
        $numericColumns = ['A', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
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
        // $sheet->getStyle('A' . $summaryStartRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $summaryStartRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE6F3FF');
        
        $summaryRow = $summaryStartRow + 2;
        
        // Monthly rental fee
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Phí thuê xe tháng:');
        $sheet->setCellValue('E' . $summaryRow, number_format($this->carRental->monthly_rental_fee ?? 0, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Overtime costs
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí tăng ca (' . number_format($totalOvertimeHours, 2) . ' giờ x ' . number_format($this->carRental->overtime_fee_per_hour ?? 50000) . ' VND):');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalOvertimeCost, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        
        // Add overtime details
        $summaryRow++;
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, '  Giờ làm việc: ' . ($this->carRental->start_working_hour ?? '07:00') . ' - ' . ($this->carRental->end_working_hour ?? '17:30'));
        $sheet->getStyle('A' . $summaryRow)->getFont()->setItalic(true);
        $sheet->getStyle('A' . $summaryRow)->getFont()->setSize(10);
        $summaryRow++;
        
        // Toll fees
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phụ phí cầu đường:');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalTollFeesWithVAT, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // // Parking fees
        // $sheet->setCellValue('A' . $summaryRow, 'Phí bãi xe:');
        // $sheet->setCellValue('E' . $summaryRow, number_format($totalParkingFees, 0, ',', '.') . ' VNĐ');
        // $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        // $summaryRow++;

        // Weighing fee
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí cân:');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalWeighingFee, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;

        // Testing surcharge
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí kiểm tra:');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalTestingSurcharge, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Over distance fee
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $overDistanceFee = $this->carRental->over_distance_fee ?? 0;
        $sheet->setCellValue('A' . $summaryRow, 'Phát sinh phí vượt giới hạn km:');
        $sheet->setCellValue('E' . $summaryRow, number_format($overDistanceFee, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        if ($overDistanceFee > 0) {
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, '(' . number_format($this->carRental->total_distance ?? 0, 0, ',', '.') . ' km - ' . number_format($this->carRental->max_distance ?? 0, 0, ',', '.') . ' km) × ' . number_format($this->carRental->over_distance_fee_per_km_unit ?? 0, 0, ',', '.') . ' VNĐ/km');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setItalic(true)->setSize(10);
        }
        $summaryRow++;
        
        // Subtotal (before VAT)
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $subtotal = ($this->carRental->monthly_rental_fee ?? 0) + $totalOvertimeCost + $totalTollFeesWithVAT + $totalWeighingFee + $totalTestingSurcharge + $overDistanceFee;
        $sheet->setCellValue('A' . $summaryRow, 'Tổng cộng (chưa thuế VAT):');
        $sheet->setCellValue('E' . $summaryRow, number_format($subtotal, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFEBCD');
        $summaryRow++;
        
        // VAT
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $vatAmount = $subtotal * 0.08;
        $sheet->setCellValue('A' . $summaryRow, 'Thuế VAT 8%:');
        $sheet->setCellValue('E' . $summaryRow, number_format($vatAmount, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('E' . $summaryRow)->getFont()->setBold(true);
        $summaryRow++;
        
        // Total with VAT
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $totalWithVat = $subtotal + $vatAmount;
        $sheet->setCellValue('A' . $summaryRow, 'Tổng cộng bao gồm thuế VAT:');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalWithVat, 0, ',', '.') . ' VNĐ');
        $sheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $summaryRow . ':E' . $summaryRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF90EE90');
        
        // Apply borders to summary section
        $summaryRange = 'A' . ($summaryStartRow + 2) . ':E' . $summaryRow;
        $sheet->getStyle($summaryRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return $sheet;
    }
} 
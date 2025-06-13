<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\ShipmentDeduction;
use App\Models\ShipmentDeductionType;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalaryExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $user;
    protected $shipments;
    protected $month;
    protected $deductionTypes;

    /**
     * @param User $user
     * @param Collection $shipments
     * @param string $month
     */
    public function __construct(User $user, Collection $shipments, string $month)
    {
        $this->user = $user;
        $this->shipments = $shipments;
        $this->month = $month;
        $this->deductionTypes = ShipmentDeductionType::all()->keyBy('id');
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Format month name in Vietnamese
        $monthDate = \DateTime::createFromFormat('Y-m', $this->month);
        $monthName = $monthDate->format('m/Y');
        
        return "Bảng lương {$monthName}";
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Format month name in Vietnamese
        $monthDate = \DateTime::createFromFormat('Y-m', $this->month);
        $monthName = $monthDate->format('m/Y');
        
        // Company information - Header
        $companyName = Setting::get('company_name', 'CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG');
        $companyAddress = Setting::get('company_address', '');
        
        // Set company information
        $sheet->setCellValue('A1', $companyName);
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress);
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        
        // Add salary title
        $sheet->setCellValue('A4', 'BẢNG LƯƠNG THÁNG ' . $monthName);
        // Note: The mergeCells is now handled dynamically below after calculating total columns
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add user information
        $userFullName = $this->user->full_name;
        $licenseNumber = $this->user->license ? $this->user->license->license_number : '';
        $userInfo = 'HỌ VÀ TÊN: ' . $userFullName;
        if (!empty($licenseNumber)) {
            $userInfo .= ' (XE ' . $licenseNumber . ')';
        }
        $sheet->setCellValue('A6', $userInfo);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        
        // Create deduction columns mapping early to use throughout the method
        $deductionColumns = [];
        $colIndex = 0;
        foreach ($this->deductionTypes as $deductionType) {
            $columnLetter = chr(ord('F') + $colIndex); // Start from column F
            $deductionColumns[$deductionType->name] = $columnLetter;
            $colIndex++;
        }
        
        // Base headers for fixed columns
        $baseHeaders = [
            'STT', 'NGÀY', 'ĐIỂM ĐI', 'ĐIỂM ĐẾN', 'LƯƠNG CƠ BẢN'
        ];
        
        // Get all deduction type names for dynamic headers
        $deductionTypeNames = $this->deductionTypes->pluck('name')->toArray();
        
        // Add GHI CHÚ as the last column header
        $notesHeader = ['GHI CHÚ'];
        
        // Calculate the number of columns for the title merge
        $totalColumns = count($baseHeaders) + count($deductionTypeNames) + count($notesHeader); // Added column for notes
        $lastHeaderColumn = chr(ord('A') + $totalColumns - 1);
        
        // Calculate the notes column letter (after all deduction columns)
        $notesColumnLetter = chr(ord('A') + count($baseHeaders) + count($deductionTypeNames));
        
        // Update the title cell merge to match the actual number of columns
        $sheet->mergeCells('A4:' . $lastHeaderColumn . '4');
        $sheet->mergeCells('A6:' . $lastHeaderColumn . '6');
        
        // Final headers including deduction types and notes column
        $headers = array_merge(
            $baseHeaders,
            $deductionTypeNames,
            $notesHeader
        );
        
        // Set headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '8', $header);
            $col++;
        }
        
        // Set column widths

        $sheet->getColumnDimension('A')->setAutoSize(false);
        $sheet->getColumnDimension('A')->setWidth(5);  // STT - extremely narrow
        // $sheet->getColumnDimension('A')->setWidth(4);  // STT
        $sheet->getColumnDimension('B')->setWidth(12); // NGÀY
        $sheet->getColumnDimension('C')->setWidth(20); // ĐIỂM ĐI
        $sheet->getColumnDimension('D')->setWidth(20); // ĐIỂM ĐẾN
        $sheet->getColumnDimension('E')->setWidth(15); // LƯƠNG CƠ BẢN
        
        // Set widths for dynamic deduction columns
        foreach ($deductionColumns as $name => $columnLetter) {
            $sheet->getColumnDimension($columnLetter)->setWidth(15); // Deduction columns
        }
        
        // Set width for notes column (last column)
        $sheet->getColumnDimension($notesColumnLetter)->setWidth(25); // GHI CHÚ
        
        // Style the header row
        $sheet->getStyle('A8:' . $lastHeaderColumn . '8')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2EFDA',
                ],
            ],
        ]);
        
        // Data rows starting from row 9
        $row = 9;
        $count = 1;
        
        // First row - Base salary
        $sheet->setCellValue('A' . $row, '-');
        $sheet->setCellValue('B' . $row, '-');
        $sheet->setCellValue('C' . $row, '-');
        $sheet->setCellValue('D' . $row, '-');
        $sheet->setCellValue('E' . $row, $this->user->salary_base);
        
        // Format the base salary cell
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        
        // Move to next row for shipment data
        $row++;
        $count = 1;
        
        // Group shipments by date to avoid duplicates
        $groupedShipments = $this->shipments->groupBy(function ($shipment) {
            return Carbon::parse($shipment->departure_time)->format('Y-m-d');
        });
        
        foreach ($groupedShipments as $date => $dateShipments) {
            foreach ($dateShipments as $shipment) {
                $sheet->setCellValue('A' . $row, $count);
                $sheet->setCellValue('B' . $row, Carbon::parse($shipment->departure_time)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $shipment->origin);
                $sheet->setCellValue('D' . $row, $shipment->destination);
                $sheet->setCellValue('E' . $row, '');
                
                // Get all deductions for this shipment (both driver_and_busboy and expense types)
                $deductions = ShipmentDeduction::where('shipment_id', $shipment->id)
                    ->get();
                
                // Fill in deduction values
                foreach ($deductions as $deduction) {
                    $deductionType = $this->deductionTypes[$deduction->shipment_deduction_type_id] ?? null;
                    if ($deductionType && isset($deductionColumns[$deductionType->name])) {
                        $col = $deductionColumns[$deductionType->name];
                        $sheet->setCellValue($col . $row, $deduction->amount);
                        $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
                
                // Add notes from shipment to the notes column
                $sheet->setCellValue($notesColumnLetter . $row, $shipment->notes);
                
                $row++;
                $count++;
            }
        }
        
        // Calculate total rows
        $lastDataRow = $row - 1;
        
        // Calculate sum of all deduction columns and store total expenses
        $totalDeductions = 0;
        $totalExpenses = 0;
        foreach ($deductionColumns as $deductionName => $column) {
            // Sum the column
            $columnSum = 0;
            for ($i = 7; $i <= $lastDataRow; $i++) { // Start from row 7 (after base salary row)
                $cellValue = $sheet->getCell($column . $i)->getValue();
                if (is_numeric($cellValue)) {
                    $columnSum += $cellValue;
                    
                    // Check if this is an expense type deduction
                    $deductionType = $this->deductionTypes->firstWhere('name', $deductionName);
                    if ($deductionType && $deductionType->type === 'expense') {
                        $totalExpenses += $cellValue;
                    }
                }
            }
            $totalDeductions += $columnSum;
        }
        
        // Add base salary row with deduction sums
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'LƯƠNG CƠ BẢN:');
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $totalRow, $this->user->salary_base);
        $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Add deduction sums to the same row
        foreach ($deductionColumns as $deductionName => $column) {
            $columnSum = 0;
            for ($i = 7; $i <= $lastDataRow; $i++) {
                $cellValue = $sheet->getCell($column . $i)->getValue();
                if (is_numeric($cellValue)) {
                    $columnSum += $cellValue;
                }
            }
            $sheet->setCellValue($column . $totalRow, $columnSum);
            $sheet->getStyle($column . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Add empty cell in notes column for the total row
        $sheet->setCellValue($notesColumnLetter . $totalRow, '');
        
        // Move to next row for total
        $row++;
        $totalRow = $row;
        
        // Add total row
        $sheet->setCellValue('A' . $totalRow, 'TỔNG LƯƠNG CB + PHỤ CẤP TÀI + CƠM NGÀY:');
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Calculate total salary (Lương Cơ Bản + Tổng chi phí)
        $totalSalary = $this->user->salary_base + $totalDeductions;
        
        // Add the total salary amount in column E
        $sheet->setCellValue('E' . $totalRow, $totalSalary);
        $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Move to next row for insurance deduction
        $row++;
        $insuranceRow = $row;
        
        // Add insurance deduction row (10% of total: base salary + deductions)
        $totalBeforeInsurance = $this->user->salary_base + $totalDeductions;
        $insuranceAmount = $totalBeforeInsurance * 0.1; // 10% of total
        $sheet->setCellValue('A' . $insuranceRow, 'TRỪ BHXH (10%):');
        $sheet->mergeCells('A' . $insuranceRow . ':D' . $insuranceRow);
        $sheet->getStyle('A' . $insuranceRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $insuranceRow, $insuranceAmount);
        $sheet->getStyle('E' . $insuranceRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Move to next row for remaining salary
        $row++;
        $remainingSalaryRow = $row;
        
        // Add remaining salary row (total before insurance - insurance)
        $remainingSalary = $totalBeforeInsurance - $insuranceAmount;
        $sheet->setCellValue('A' . $remainingSalaryRow, 'TỔNG LƯƠNG CÒN LẠI:');
        $sheet->mergeCells('A' . $remainingSalaryRow . ':D' . $remainingSalaryRow);
        $sheet->getStyle('A' . $remainingSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $remainingSalaryRow, $remainingSalary);
        $sheet->getStyle('E' . $remainingSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Style the additional calculation rows
        $calculationStartRow = $totalRow;
        $calculationEndRow = $row;
        $calculationRange = 'A' . $calculationStartRow . ':E' . $calculationEndRow;
        $sheet->getStyle($calculationRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Highlight TRỪ BHXH row with yellow background
        $sheet->getStyle('A' . $insuranceRow . ':E' . $insuranceRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the insurance row
        $sheet->setCellValue($notesColumnLetter . $insuranceRow, '');
        
        // Highlight TỔNG LƯƠNG CÒN LẠI row with yellow background
        $sheet->getStyle('A' . $remainingSalaryRow . ':E' . $remainingSalaryRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the remaining salary row
        $sheet->setCellValue($notesColumnLetter . $remainingSalaryRow, '');
        
        // Set number formats for base salary column
        $sheet->getStyle('E6:E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set number formats for deduction columns
        foreach ($deductionColumns as $column) {
            $sheet->getStyle($column . '6:' . $column . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Set text alignment
        $sheet->getStyle('A6:B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Set alignment for notes column
        $sheet->getStyle($notesColumnLetter . '6:' . $notesColumnLetter . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle($notesColumnLetter . '6:' . $notesColumnLetter . $totalRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle($notesColumnLetter . '6:' . $notesColumnLetter . $totalRow)->getAlignment()->setWrapText(true);
        
        // Set alignment for deduction columns
        foreach ($deductionColumns as $column) {
            $sheet->getStyle($column . '6:' . $column . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return $sheet;
    }
}

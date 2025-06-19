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
use App\Models\SalaryAdvanceRequest;
USE App\Constants;

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
        $this->deductionTypes = ShipmentDeductionType::where('status', 'active')->get()->keyBy('id');
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
            $sheet->getColumnDimension($columnLetter)->setWidth(20); // Deduction columns
        }
        
        // Set width for notes column (last column)
        $sheet->getColumnDimension($notesColumnLetter)->setWidth(15); // GHI CHÚ
        
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
        
        // Apply special formatting to the base salary row
        $baseSalaryRowRange = 'A' . $row . ':' . $lastHeaderColumn . $row;
        $sheet->getStyle($baseSalaryRowRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F2F2F2',
                ],
            ],
        ]);
        
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
        
        // Apply table borders to the entire data section (from header row to total row)
        $dataRange = 'A8:' . $lastHeaderColumn . $totalRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ],
            ],
        ]);
        
        // Apply special formatting to the total row
        $totalRowRange = 'A' . $totalRow . ':' . $lastHeaderColumn . $totalRow;
        $sheet->getStyle($totalRowRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2EFDA',
                ],
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ],
            ],
        ]);
        
        // Move to next row for total
        $row++;
        $totalRow = $row;
        
        // Get salary advance data for the month
        $startDate = Carbon::parse($this->month)->startOfMonth();
        $endDate = Carbon::parse($this->month)->endOfMonth();
        $totalTypeSalary = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $startDate, $endDate);
        $totalTypeBonus = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $startDate, $endDate);
        $totalTypePenalty = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $startDate, $endDate);
        
        // Add LƯƠNG CƠ BẢN row (base salary with deduction sums)
        $baseSalaryRow = $row++;
        $sheet->setCellValue('A' . $baseSalaryRow, 'LƯƠNG CƠ BẢN:');
        $sheet->mergeCells('A' . $baseSalaryRow . ':D' . $baseSalaryRow);
        $sheet->getStyle('A' . $baseSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $baseSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $baseSalaryRow, $this->user->salary_base);
        $sheet->getStyle('E' . $baseSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight LƯƠNG CƠ BẢN row with yellow background
        $sheet->getStyle('A' . $baseSalaryRow . ':E' . $baseSalaryRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the base salary row
        $sheet->setCellValue($notesColumnLetter . $baseSalaryRow, '');
        
        // Add TỔNG LƯƠNG CB + PHỤ CẤP TÀI + CƠM NGÀY row
        $totalSalaryRow = $row++;
        $totalSalary = $this->user->salary_base + $totalDeductions;
        $sheet->setCellValue('A' . $totalSalaryRow, 'TỔNG LƯƠNG CB + PHỤ CẤP TÀI + CƠM NGÀY:');
        $sheet->mergeCells('A' . $totalSalaryRow . ':D' . $totalSalaryRow);
        $sheet->getStyle('A' . $totalSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $totalSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $totalSalaryRow, $totalSalary);
        $sheet->getStyle('E' . $totalSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight TỔNG LƯƠNG CB + PHỤ CẤP TÀI + CƠM NGÀY row with yellow background
        $sheet->getStyle('A' . $totalSalaryRow . ':E' . $totalSalaryRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the total salary row
        $sheet->setCellValue($notesColumnLetter . $totalSalaryRow, '');
        
        // Add THƯỞNG row
        $bonusRow = $row++;
        $sheet->setCellValue('A' . $bonusRow, 'THƯỞNG:');
        $sheet->mergeCells('A' . $bonusRow . ':D' . $bonusRow);
        $sheet->getStyle('A' . $bonusRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $bonusRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $bonusRow, $totalTypeBonus);
        $sheet->getStyle('E' . $bonusRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight THƯỞNG row with yellow background
        $sheet->getStyle('A' . $bonusRow . ':E' . $bonusRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the bonus row
        $sheet->setCellValue($notesColumnLetter . $bonusRow, '');
        
        // Add ĐÃ ỨNG LƯƠNG row
        $advanceSalaryRow = $row++;
        $sheet->setCellValue('A' . $advanceSalaryRow, 'TRỪ ỨNG LƯƠNG:');
        $sheet->mergeCells('A' . $advanceSalaryRow . ':D' . $advanceSalaryRow);
        $sheet->getStyle('A' . $advanceSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $advanceSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $advanceSalaryRow, $totalTypeSalary);
        $sheet->getStyle('E' . $advanceSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight TỔNG ỨNG LƯƠNG row with yellow background
        $sheet->getStyle('A' . $advanceSalaryRow . ':E' . $advanceSalaryRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the advance salary row
        $sheet->setCellValue($notesColumnLetter . $advanceSalaryRow, '');
        
        // Add PHẠT row
        $penaltyRow = $row++;
        $sheet->setCellValue('A' . $penaltyRow, 'TRỪ PHẠT:');
        $sheet->mergeCells('A' . $penaltyRow . ':D' . $penaltyRow);
        $sheet->getStyle('A' . $penaltyRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $penaltyRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $penaltyRow, $totalTypePenalty);
        $sheet->getStyle('E' . $penaltyRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight PHẠT row with yellow background
        $sheet->getStyle('A' . $penaltyRow . ':E' . $penaltyRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFF00'],
            ],
        ]);
        
        // Add empty cell in notes column for the penalty row
        $sheet->setCellValue($notesColumnLetter . $penaltyRow, '');
        
        // Calculate total before insurance
        $totalBeforeInsurance = ($this->user->salary_base + $totalDeductions + $totalTypeBonus) - ($totalTypeSalary + $totalTypePenalty);
        
        // Add TRỪ BHXH row
        $insuranceRow = $row++;
        $insuranceDeduction = $totalBeforeInsurance * (Constants::TAX_IN_VAT/100); // 10% of total
        $sheet->setCellValue('A' . $insuranceRow, 'TRỪ BHXH (10%):');
        $sheet->mergeCells('A' . $insuranceRow . ':D' . $insuranceRow);
        $sheet->getStyle('A' . $insuranceRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $insuranceRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $insuranceRow, $insuranceDeduction);
        $sheet->getStyle('E' . $insuranceRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Add TỔNG LƯƠNG CÒN LẠI row
        $remainingSalaryRow = $row++;
        $totalSalaryRemaining = $totalBeforeInsurance - $insuranceDeduction;
        $sheet->setCellValue('A' . $remainingSalaryRow, 'TỔNG LƯƠNG CÒN LẠI:');
        $sheet->mergeCells('A' . $remainingSalaryRow . ':D' . $remainingSalaryRow);
        $sheet->getStyle('A' . $remainingSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $remainingSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('E' . $remainingSalaryRow, $totalSalaryRemaining);
        $sheet->getStyle('E' . $remainingSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Style the additional calculation rows (separate from the main table)
        $calculationStartRow = $baseSalaryRow;
        $calculationEndRow = $remainingSalaryRow;
        $calculationRange = 'A' . $calculationStartRow . ':E' . $calculationEndRow;
        $sheet->getStyle($calculationRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
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
        
        // Set number formats for base salary column (only for the data table, not calculation section)
        $sheet->getStyle('E9:E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set number formats for deduction columns
        foreach ($deductionColumns as $column) {
            $sheet->getStyle($column . '9:' . $column . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        // Set text alignment
        $sheet->getStyle('A9:B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E9:E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Set alignment for notes column
        $sheet->getStyle($notesColumnLetter . '9:' . $notesColumnLetter . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle($notesColumnLetter . '9:' . $notesColumnLetter . $totalRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle($notesColumnLetter . '9:' . $notesColumnLetter . $totalRow)->getAlignment()->setWrapText(true);
        
        // Set alignment for deduction columns
        foreach ($deductionColumns as $column) {
            $sheet->getStyle($column . '9:' . $column . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return $sheet;
    }
}

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
use App\Constants;
use App\Enum\SalaryType;
use Illuminate\Support\Facades\Log;

class SalaryCommissionExport implements WithTitle, WithStyles, ShouldAutoSize
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
        
        // Get deduction types and limit to prevent Excel errors
        $allDeductionTypes = ShipmentDeductionType::where('status', 'active')->get();
        
        // Check for duplicate names
        $allNames = $allDeductionTypes->pluck('name')->toArray();
        $duplicateNames = array_diff_assoc($allNames, array_unique($allNames));
        
        if (!empty($duplicateNames)) {
            Log::warning('Found duplicate deduction type names', [
                'duplicate_names' => array_values($duplicateNames),
                'total_types' => $allDeductionTypes->count(),
                'unique_names' => count(array_unique($allNames))
            ]);
        }
        
        // Limit to maximum 690 deduction types (5 base + 690 deduction + 1 notes = 696 total, under 702 limit)
        if ($allDeductionTypes->count() > 690) {
            Log::warning('Too many deduction types, limiting to 690 to prevent Excel errors', [
                'total_types' => $allDeductionTypes->count(),
                'limited_to' => 690
            ]);
            $this->deductionTypes = $allDeductionTypes->take(690)->keyBy('id');
        } else {
            $this->deductionTypes = $allDeductionTypes->keyBy('id');
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Format month name in Vietnamese
        $monthDate = \DateTime::createFromFormat('m/Y', $this->month);
        if ($monthDate === false) {
            // Fallback nếu format không đúng
            return "Bảng lương doanh số {$this->month}";
        }
        $monthName = $monthDate->format('m/Y');
        
        return "Bảng lương doanh số {$monthName}";
    }

    /**
     * Convert column number to Excel column letter
     * Supports up to 702 columns (A-ZZ)
     * 
     * @param int $columnNumber
     * @return string
     */
    private function getColumnLetter(int $columnNumber): string
    {
        if ($columnNumber <= 0) {
            throw new \InvalidArgumentException("Column number must be positive");
        }
        
        if ($columnNumber <= 26) {
            // A-Z (1-26)
            return chr(ord('A') + $columnNumber - 1);
        } elseif ($columnNumber <= 702) {
            // AA-ZZ (27-702)
            $firstChar = chr(ord('A') + (($columnNumber - 1) / 26) - 1);
            $secondChar = chr(ord('A') + (($columnNumber - 1) % 26));
            return $firstChar . $secondChar;
        } else {
            throw new \InvalidArgumentException("Column number {$columnNumber} exceeds Excel limit (702)");
        }
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Format month name in Vietnamese
        $monthDate = \DateTime::createFromFormat('m/Y', $this->month);
        if ($monthDate === false) {
            // Fallback nếu format không đúng
            $monthName = $this->month;
        } else {
            $monthName = $monthDate->format('m/Y');
        }
        
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
        $sheet->setCellValue('A4', 'BẢNG LƯƠNG DOANH SỐ THÁNG ' . $monthName);
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
        $userInfo .= ' - LƯƠNG DOANH SỐ (' . $this->user->getSalaryByPercent() . '%)';
        $sheet->setCellValue('A6', $userInfo);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        
        // Create deduction columns mapping early to use throughout the method
        $deductionColumns = [];
        $colIndex = 0;
        
        // Get unique deduction type names to avoid duplicates
        $uniqueDeductionTypes = $this->deductionTypes->pluck('name')->unique()->values();
        
        foreach ($uniqueDeductionTypes as $deductionTypeName) {
            // Start from column L (12th column), so we need column number 12 + colIndex
            $columnNumber = 12 + $colIndex; // L=12, M=13, N=14, etc.
            $columnLetter = $this->getColumnLetter($columnNumber);
            $deductionColumns[$deductionTypeName] = $columnLetter;
            $colIndex++;
        }
        
        // Debug logging for deduction columns
        Log::info('SalaryExport DeductionColumns', [
            'deductionColumns' => $deductionColumns,
            'total_deduction_types' => count($this->deductionTypes),
            'unique_deduction_types' => count($uniqueDeductionTypes),
            'duplicates_removed' => count($this->deductionTypes) - count($uniqueDeductionTypes)
        ]);
        
        // Base headers for fixed columns
        $baseHeaders = [
            'STT', 'NGÀY', 'CÔNG TY', 'ĐIỂM ĐI', 'ĐIỂM ĐẾN', 'HÀNG HÓA', 'KM', 'GIÁ CHO TÀI XẾ', 'KHỐI LƯỢNG THỰC TẾ(KG)', 'THÀNH TIỀN(bao gồm cẩu)', 'THÀNH TIỀN'
        ];
        
        // Get all deduction type names for dynamic headers (remove duplicates)
        $deductionTypeNames = $this->deductionTypes->pluck('name')->unique()->values()->toArray();
        
        // Add GHI CHÚ as the last column header
        $notesHeader = ['GHI CHÚ'];
        
        // Calculate the number of columns for the title merge
        $totalColumns = count($baseHeaders) + count($deductionTypeNames) + count($notesHeader); // Added column for notes
        
        // Debug logging
        Log::info('SalaryExport Debug', [
            'baseHeaders' => count($baseHeaders),
            'deductionTypeNames' => count($deductionTypeNames),
            'notesHeader' => count($notesHeader),
            'totalColumns' => $totalColumns
        ]);
        
        // Validate total columns to prevent Excel errors (max 702 columns = 26*27)
        if ($totalColumns > 702) {
            throw new \Exception("Quá nhiều cột: {$totalColumns}. Excel chỉ hỗ trợ tối đa 702 cột (ZZ).");
        }
        
        // Calculate the last header column letter
        $lastHeaderColumn = $this->getColumnLetter($totalColumns);
        
        // Calculate the notes column letter (after all deduction columns)
        // Base headers: 11 cột (A-K), Deduction types: 12 cột (L-W), Notes: cột X (24)
        $notesColumnLetter = $this->getColumnLetter($totalColumns);
        
        // Debug logging for column letters
        Log::info('SalaryExport Column Letters', [
            'lastHeaderColumn' => $lastHeaderColumn,
            'notesColumnLetter' => $notesColumnLetter,
            'totalColumns' => $totalColumns,
            'baseHeadersCount' => count($baseHeaders),
            'deductionTypeNamesCount' => count($deductionTypeNames),
            'notesHeaderCount' => count($notesHeader),
            'calculation' => 'notesColumnLetter = getColumnLetter(' . $totalColumns . ') = ' . $notesColumnLetter
        ]);
        
        // Update the title cell merge to match the actual number of columns
        try {
            $sheet->mergeCells('A4:' . $lastHeaderColumn . '4');
            $sheet->mergeCells('A6:' . $lastHeaderColumn . '6');
        } catch (\Exception $e) {
            Log::error('Failed to merge cells in SalaryExport', [
                'lastHeaderColumn' => $lastHeaderColumn,
                'totalColumns' => $totalColumns,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Lỗi merge cells: {$e->getMessage()}. Cột cuối: {$lastHeaderColumn}, Tổng cột: {$totalColumns}");
        }
        
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
        $sheet->getColumnDimension('C')->setWidth(20); // CÔNG TY
        $sheet->getColumnDimension('D')->setWidth(20); // ĐIỂM ĐI
        $sheet->getColumnDimension('E')->setWidth(20); // ĐIỂM ĐẾN
        $sheet->getColumnDimension('F')->setWidth(15); // HÀNG HÓA
        $sheet->getColumnDimension('G')->setWidth(10); // KM
        $sheet->getColumnDimension('H')->setWidth(15); // GIÁ CHO TÀI XẾ
        $sheet->getColumnDimension('I')->setWidth(20); // KHỐI LƯỢNG THỰC TẾ(KG)
        $sheet->getColumnDimension('J')->setWidth(20); // THÀNH TIỀN(bao gồm cẩu)
        $sheet->getColumnDimension('K')->setWidth(15); // THÀNH TIỀN
        
        // Set widths for dynamic deduction columns
        foreach ($deductionColumns as $name => $columnLetter) {
            // Validate column letter before using
            if (!preg_match('/^[A-Z]+$/', $columnLetter)) {
                Log::error('Invalid column letter in deductionColumns', [
                    'name' => $name,
                    'columnLetter' => $columnLetter,
                    'deductionColumns' => $deductionColumns
                ]);
                throw new \Exception("Ký tự cột không hợp lệ: '{$columnLetter}' cho deduction type '{$name}'");
            }
            $sheet->getColumnDimension($columnLetter)->setWidth(20); // Deduction columns
        }
        
        // Set width for notes column (last column)
        // Validate notes column letter
        if (!preg_match('/^[A-Z]+$/', $notesColumnLetter)) {
            Log::error('Invalid notes column letter', [
                'notesColumnLetter' => $notesColumnLetter,
                'baseHeaders' => count($baseHeaders),
                'deductionTypeNames' => count($deductionTypeNames)
            ]);
            throw new \Exception("Ký tự cột notes không hợp lệ: '{$notesColumnLetter}'");
        }
        $sheet->getColumnDimension($notesColumnLetter)->setWidth(15); // GHI CHÚ
        
        // Validate last header column letter
        if (!preg_match('/^[A-Z]+$/', $lastHeaderColumn)) {
            Log::error('Invalid last header column letter', [
                'lastHeaderColumn' => $lastHeaderColumn,
                'totalColumns' => $totalColumns
            ]);
            throw new \Exception("Ký tự cột cuối không hợp lệ: '{$lastHeaderColumn}'");
        }
        
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
        
        // Group shipments by date to avoid duplicates
        $groupedShipments = $this->shipments->groupBy(function ($shipment) {
            return Carbon::parse($shipment->departure_time)->format('Y-m-d');
        });
        
        foreach ($groupedShipments as $date => $dateShipments) {
            foreach ($dateShipments as $shipment) {
                $sheet->setCellValue('A' . $row, $count);
                $sheet->setCellValue('B' . $row, Carbon::parse($shipment->departure_time)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $shipment->customer->name ?? '-'); // CÔNG TY
                $sheet->setCellValue('D' . $row, $shipment->origin);
                $sheet->setCellValue('E' . $row, $shipment->destination);
                // HÀNG HÓA = phân tách nhau bằng dấu ',' từ relationship goods
                $goodsNames = $shipment->goods->pluck('name')->implode(', ');
                $sheet->setCellValue('F' . $row, $goodsNames ?: '-'); // HÀNG HÓA
                $sheet->setCellValue('G' . $row, $shipment->distance ?? '-'); // KM
                // Hiển thị unit_price_for_driver, nếu null/0 thì hiển thị unit_price để tham khảo
                $displayPrice = $shipment->unit_price_for_driver ?? $shipment->unit_price ?? '-';
                $sheet->setCellValue('H' . $row, $displayPrice); // GIÁ CHO TÀI XẾ
                $sheet->setCellValue('I' . $row, $shipment->actual_weight ?? '-'); // KHỐI LƯỢNG THỰC TẾ(KG)
                
                // THÀNH TIỀN(bao gồm cẩu) = (Giá chuyến cho tài xế × Số lượng chuyến) + Chi phí chuyến xe
                // Chỉ sử dụng unit_price_for_driver, nếu null hoặc 0 thì giữ 0
                $unitPrice = (float)($shipment->unit_price_for_driver ?? 0);
                $tripCount = (int)($shipment->trip_count ?? 1);
                $tripValue = $unitPrice * $tripCount;
                
                // Tính chi phí chuyến xe (expense type)
                $expenseCosts = ShipmentDeduction::where('shipment_id', $shipment->id)
                    ->whereHas('shipmentDeductionType', function($query) {
                        $query->where('type', 'expense');
                    })
                    ->sum('amount');
                
                $totalWithCrane = $tripValue + $expenseCosts;
                $sheet->setCellValue('J' . $row, $totalWithCrane);
                
                // THÀNH TIỀN = (Giá chuyến × Số lượng chuyến) - chỉ giá trị chuyến xe
                $totalAmount = $tripValue;
                $sheet->setCellValue('K' . $row, $totalAmount);
                

                
                // Get all deductions for this shipment
                // 1. User-specific deductions (driver_and_busboy types)
                $userDeductions = ShipmentDeduction::where('shipment_id', $shipment->id)
                    ->where('user_id', $this->user->id)
                    ->get();
                
                // 2. Expense deductions (không filter theo user_id - chi phí chung)
                $expenseDeductions = ShipmentDeduction::where('shipment_id', $shipment->id)
                    ->whereHas('shipmentDeductionType', function($query) {
                        $query->where('type', 'expense');
                    })
                    ->get();
                
                // Merge both types of deductions
                $deductions = $userDeductions->merge($expenseDeductions);
                
                // Fill in deduction values
                foreach ($deductions as $deduction) {
                    $deductionType = $this->deductionTypes[$deduction->shipment_deduction_type_id] ?? null;
                    if ($deductionType && isset($deductionColumns[$deductionType->name])) {
                        $col = $deductionColumns[$deductionType->name];
                        
                        // Validate column letter before using
                        if (!preg_match('/^[A-Z]+$/', $col)) {
                            Log::error('Invalid column letter in data loop', [
                                'col' => $col,
                                'deductionType' => $deductionType->name,
                                'deductionColumns' => $deductionColumns
                            ]);
                            continue; // Skip this deduction instead of crashing
                        }
                        
                        $sheet->setCellValue($col . $row, $deduction->amount);
                        $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
                

                
                // Format number columns
                $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0'); // KM
                $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0'); // GIÁ
                $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0'); // KHỐI LƯỢNG THỰC TẾ(KG)
                $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0'); // THÀNH TIỀN(bao gồm cẩu)
                $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0'); // THÀNH TIỀN
                

                
                // Add notes from shipment to the notes column
                // Validate notes column letter before using
                if (preg_match('/^[A-Z]+$/', $notesColumnLetter)) {
                    $sheet->setCellValue($notesColumnLetter . $row, $shipment->notes);
                    
                    // Debug log cho shipment 57
                    if ($shipment->id == 57) {
                        Log::info('Debug Notes Column Mapping', [
                            'shipment_id' => $shipment->id,
                            'notesColumnLetter' => $notesColumnLetter,
                            'notes_value' => $shipment->notes,
                            'cell_address' => $notesColumnLetter . $row,
                            'deductionColumns' => $deductionColumns
                        ]);
                    }
                } else {
                    Log::error('Invalid notes column letter in data loop', [
                        'notesColumnLetter' => $notesColumnLetter,
                        'row' => $row
                    ]);
                }
                

                
                $row++;
                $count++;
            }
        }
        
        // Calculate total rows
        $lastDataRow = $row - 1;
        
        // Calculate sum of all deduction columns and store total expenses
        $totalDeductions = 0;
        $totalExpenses = 0; // Tính chi phí chuyến hàng cho THÀNH TIỀN(bao gồm cẩu)
        foreach ($deductionColumns as $deductionName => $column) {
            // Sum the column
            $columnSum = 0;
            for ($i = 7; $i <= $lastDataRow; $i++) { // Start from row 7 (after base salary row)
                $cellValue = $sheet->getCell($column . $i)->getValue();
                if (is_numeric($cellValue)) {
                    $columnSum += $cellValue;
                    
                    // Không tính chi phí chuyến hàng vào lương nhân viên
                    // Check if this is an expense type deduction
                    // $deductionType = $this->deductionTypes->firstWhere('name', $deductionName);
                    // if ($deductionType && $deductionType->type === 'expense') {
                    //     $totalExpenses += $cellValue;
                    // }
                }
            }
            $totalDeductions += $columnSum;
        }
        
        // Add base salary row with deduction sums
        $totalRow = $row;
        
        // Add deduction sums to the same rowf
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
        
        // Bỏ row LƯƠNG DOANH SỐ (X%) theo yêu cầu
        // $baseSalaryRow = $row++; // Không tăng row nữa
        
        // Add TỔNG LƯƠNG DS + PHỤ CẤP TÀI + CƠM NGÀY row
        $totalSalaryRow = $row++;
        
        // Calculate total salary: commission + user-specific deductions only
        // Tính tổng giá trị chuyến xe: sum(unit_price_for_driver * trip_count) cho commission salary
        $totalTripValue = 0;
        foreach ($this->shipments as $shipment) {
            // Chỉ sử dụng unit_price_for_driver, nếu null hoặc 0 thì giữ 0
            $unitPrice = $shipment->unit_price_for_driver ?? 0;
            $tripCount = $shipment->trip_count ?? 1;
            $totalTripValue += ($unitPrice * $tripCount);
        }
        
        $commissionRate = $this->user->getSalaryByPercent() / 100; // Get user's commission rate
        $commissionSalary = $totalTripValue * $commissionRate;
        
        // Tính tổng trợ cấp của user (sum(ShipmentDeduction.user_id == user_id))
        // $userSpecificDeductions = 0;
        // foreach ($deductionColumns as $deductionName => $column) {
        //     $deductionType = $this->deductionTypes->firstWhere('name', $deductionName);
        //     if ($deductionType && $deductionType->type !== 'expense') {
        //         $columnSum = 0;
        //         for ($i = 9; $i <= $lastDataRow; $i++) { // Start from row 9 (data rows)
        //             $cellValue = $sheet->getCell($column . $i)->getValue();
        //             if (is_numeric($cellValue)) {
        //                 $columnSum += $cellValue;
        //             }
        //         }
        //         $userSpecificDeductions += $columnSum;
        //     }
        // }

        // Tính tổng từng loại phụ cấp từ deductionTypeNames
        $deductionDetails = [];
        $totalUserSpecificDeductions = 0;
        
        foreach ($deductionColumns as $deductionName => $column) {
            $deductionType = $this->deductionTypes->firstWhere('name', $deductionName);
            if ($deductionType && $deductionType->type !== 'expense') {
                $columnSum = 0;
                for ($i = 9; $i <= $lastDataRow; $i++) { // Start from row 9 (data rows)
                    $cellValue = $sheet->getCell($column . $i)->getValue();
                    if (is_numeric($cellValue)) {
                        $columnSum += $cellValue;
                    }
                }
                $deductionDetails[$deductionName] = $columnSum;
                $totalUserSpecificDeductions += $columnSum;
            }
        }
        
        // TỔNG LƯƠNG DS + PHỤ CẤP TÀI + CƠM NGÀY = ({$this->user->getSalaryByPercent()}% của sum(THÀNH TIỀN)) + Tổng trợ cấp của user
        $totalSalary = $commissionSalary + $totalUserSpecificDeductions;
        
        // Debug log
        Log::info('Debug TotalSalary Calculation', [
            'user_id' => $this->user->id,
            'user_commission_rate' => $this->user->getSalaryByPercent(),
            'totalTripValue' => $totalTripValue,
            'commissionRate' => $commissionRate,
            'commissionSalary' => $commissionSalary,
            'deductionDetails' => $deductionDetails,
            'totalUserSpecificDeductions' => $totalUserSpecificDeductions,
            'totalSalary' => $totalSalary,
            'calculation' => 'TỔNG LƯƠNG DS + PHỤ CẤP TÀI + CƠM NGÀY = (' . $commissionSalary . ') + (' . $totalUserSpecificDeductions . ') = ' . $totalSalary,
            'formula' => '(' . $this->user->getSalaryByPercent() . '% của sum(THÀNH TIỀN)) + Tổng trợ cấp của user'
        ]);
        
        // Hiển thị chi tiết từng loại phụ cấp
        foreach ($deductionDetails as $deductionName => $amount) {
            if ($amount > 0) { // Chỉ hiển thị những loại có số tiền > 0
                $deductionRow = $row++;
                $sheet->setCellValue('A' . $deductionRow, $deductionName . ':');
                $sheet->mergeCells('A' . $deductionRow . ':J' . $deductionRow);
                $sheet->getStyle('A' . $deductionRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $deductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('K' . $deductionRow, $amount);
                $sheet->getStyle('K' . $deductionRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Highlight deduction row with light blue background
                $sheet->getStyle('A' . $deductionRow . ':K' . $deductionRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        // 'color' => ['rgb' => 'E6F3FF'], // Light blue
                    ],
                ]);
                
                // Add empty cell in notes column for the deduction row
                $sheet->setCellValue($notesColumnLetter . $deductionRow, '');
            }
        }
        
        // Thêm dòng TỔNG LƯƠNG DT (salary_by_percent)
        $commissionSalaryRow = $row++;
        $sheet->setCellValue('A' . $commissionSalaryRow, 'TỔNG LƯƠNG DT (' . $this->user->getSalaryByPercent() . '%):');
        $sheet->mergeCells('A' . $commissionSalaryRow . ':J' . $commissionSalaryRow);
        $sheet->getStyle('A' . $commissionSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $commissionSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $commissionSalaryRow, $commissionSalary);
        $sheet->getStyle('K' . $commissionSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight TỔNG LƯƠNG DT row (no background color)
        
        // Add empty cell in notes column for the commission salary row
        $sheet->setCellValue($notesColumnLetter . $commissionSalaryRow, '');
        
        // Thêm dòng TỔNG LƯƠNG (commission + phụ cấp)
        $totalSalaryRow = $row++;
        $sheet->setCellValue('A' . $totalSalaryRow, 'TỔNG LƯƠNG:');
        $sheet->mergeCells('A' . $totalSalaryRow . ':J' . $totalSalaryRow);
        $sheet->getStyle('A' . $totalSalaryRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));
        $sheet->getStyle('A' . $totalSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $totalSalaryRow, $totalSalary);
        $sheet->getStyle('K' . $totalSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K' . $totalSalaryRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));
        
        // Highlight TỔNG LƯƠNG row (no background color)
        
        // Add empty cell in notes column for the total salary row
        $sheet->setCellValue($notesColumnLetter . $totalSalaryRow, '');
        
        // Add THƯỞNG row
        $bonusRow = $row++;
        $sheet->setCellValue('A' . $bonusRow, 'THƯỞNG:');
        $sheet->mergeCells('A' . $bonusRow . ':J' . $bonusRow);
        $sheet->getStyle('A' . $bonusRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $bonusRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $bonusRow, $totalTypeBonus);
        $sheet->getStyle('K' . $bonusRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight THƯỞNG row (no background color)
        
        // Add empty cell in notes column for the bonus row
        $sheet->setCellValue($notesColumnLetter . $bonusRow, '');
        
        // Add ĐÃ ỨNG LƯƠNG row
        $advanceSalaryRow = $row++;
        $sheet->setCellValue('A' . $advanceSalaryRow, 'TRỪ ỨNG LƯƠNG:');
        $sheet->mergeCells('A' . $advanceSalaryRow . ':J' . $advanceSalaryRow);
        $sheet->getStyle('A' . $advanceSalaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $advanceSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $advanceSalaryRow, $totalTypeSalary);
        $sheet->getStyle('K' . $advanceSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight TỔNG ỨNG LƯƠNG row (no background color)
        
        // Add empty cell in notes column for the advance salary row
        $sheet->setCellValue($notesColumnLetter . $advanceSalaryRow, '');
        
        // Add PHẠT row
        $penaltyRow = $row++;
        $sheet->setCellValue('A' . $penaltyRow, 'TRỪ PHẠT:');
        $sheet->mergeCells('A' . $penaltyRow . ':J' . $penaltyRow);
        $sheet->getStyle('A' . $penaltyRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $penaltyRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $penaltyRow, $totalTypePenalty);
        $sheet->getStyle('K' . $penaltyRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Highlight PHẠT row (no background color)
        
        // Add empty cell in notes column for the penalty row
        $sheet->setCellValue($notesColumnLetter . $penaltyRow, '');
        
        // TRỪ BHXH: X% của Y
        // X: settings.social_insurance_contribution_rate ?? 10.5
        // Y: settings.social_insurance_contribution_amount ?? 5500000
        $insuranceRow = $row++;
        
        // Lấy settings từ database và parse decimal
        $insuranceRate = parseDecimal(Setting::get('social_insurance_contribution_rate', 10.5));
        $insuranceAmount = parseDecimal(Setting::get('social_insurance_contribution_amount', 5500000));
        
        // Tính BHXH: X% của Y
        $insuranceDeduction = $insuranceAmount * ($insuranceRate / 100);
        
        $sheet->setCellValue('A' . $insuranceRow, 'TRỪ BHXH (' . $insuranceRate . '%) - Mức đóng lương cơ bản: ' . number_format($insuranceAmount) . ' đ');
        $sheet->mergeCells('A' . $insuranceRow . ':J' . $insuranceRow);
        $sheet->getStyle('A' . $insuranceRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $insuranceRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $insuranceRow, $insuranceDeduction);
        $sheet->getStyle('K' . $insuranceRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // TỔNG LƯƠNG CÒN LẠI: (TỔNG LƯƠNG DS + PHỤ CẤP TÀI + CƠM NGÀY) - (TRỪ BHXH + TRỪ PHẠT + TRỪ ỨNG LƯƠNG)
        // BHXH = X% của Y (từ settings), không phụ thuộc vào totalSalary
        $remainingSalaryRow = $row++;
        $totalSalaryRemaining = $totalSalary - ($insuranceDeduction + $totalTypePenalty + $totalTypeSalary);
        
        // Debug log cho BHXH và Tổng lương còn lại
        Log::info('Debug Final Calculation', [
            'totalSalary' => $totalSalary,
            'insuranceRate' => $insuranceRate,
            'insuranceAmount' => $insuranceAmount,
            'insuranceDeduction' => $insuranceDeduction,
            'totalTypePenalty' => $totalTypePenalty,
            'totalTypeSalary' => $totalTypeSalary,
            'totalSalaryRemaining' => $totalSalaryRemaining,
            'bhxh_calculation' => $insuranceRate . '% của ' . $insuranceAmount . ' = ' . $insuranceDeduction,
            'remaining_calculation' => $totalSalary . ' - (' . $insuranceDeduction . ' + ' . $totalTypePenalty . ' + ' . $totalTypeSalary . ') = ' . $totalSalaryRemaining
        ]);
        
        $sheet->setCellValue('A' . $remainingSalaryRow, 'THỰC LÃNH:');
        $sheet->mergeCells('A' . $remainingSalaryRow . ':J' . $remainingSalaryRow);
        $sheet->getStyle('A' . $remainingSalaryRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));
        $sheet->getStyle('A' . $remainingSalaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('K' . $remainingSalaryRow, $totalSalaryRemaining);
        $sheet->getStyle('K' . $remainingSalaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K' . $remainingSalaryRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000'));
        
        // Style the additional calculation rows (separate from the main table)
        // Bắt đầu từ dòng đầu tiên của phụ cấp (nếu có) hoặc từ commissionSalaryRow
        $calculationStartRow = isset($deductionDetails) && !empty($deductionDetails) ? ($commissionSalaryRow - count(array_filter($deductionDetails, function($amount) { return $amount > 0; }))) : $commissionSalaryRow;
        $calculationEndRow = $remainingSalaryRow;
        $calculationRange = 'A' . $calculationStartRow . ':K' . $calculationEndRow;
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
        
        // Highlight TRỪ BHXH row (no background color)
        
        // Add empty cell in notes column for the insurance row
        $sheet->setCellValue($notesColumnLetter . $insuranceRow, '');
        
        // Highlight TỔNG LƯƠNG CÒN LẠI row (no background color)
        
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
        $sheet->getStyle('C9:C' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // CÔNG TY
        $sheet->getStyle('D9:E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // ĐIỂM ĐI, ĐIỂM ĐẾN
        $sheet->getStyle('F9:F' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // HÀNG HÓA
        $sheet->getStyle('G9:G' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // KM
        $sheet->getStyle('H9:K' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // GIÁ, KHỐI LƯỢNG, THÀNH TIỀN
        
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

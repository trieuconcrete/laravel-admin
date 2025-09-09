<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\User;
use App\Models\Setting;
use App\Models\SalaryAdvanceRequest;
use Carbon\Carbon;
use App\Services\SalaryService;

class OfficeSalaryExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $user;
    protected $month;
    protected $salaryData;
    protected $salaryService;

    public function __construct(User $user, $month)
    {
        $this->user = $user;
        $this->month = $month;
        $this->salaryService = app(SalaryService::class);
        $this->calculateSalaryData();
    }

    private function calculateSalaryData()
    {
        // Get user salary base
        $salaryBase = $this->user->salary_base ?? 0;
        
        // Calculate working days (22 days)
        $workingDays = 22;
        
        // Calculate lunch allowance
        $dailyLunchAllowance = $this->user->getDailyLunchAllowance();
        $lunchAllowance = $this->user->getMonthlyLunchAllowance();
        
        // Get salary advance data for the month
        // Sử dụng logic tính ngày mới từ SalaryService (issue #197)
        list($month, $year) = explode('/', $this->month);
        $periodDates = $this->salaryService->calculateSalaryPeriodDates((int)$month, (int)$year);
        $startDate = $periodDates['start_date'];
        $endDate = $periodDates['end_date'];
        $totalTypeSalary = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_SALARY, $startDate, $endDate);
        $totalTypeBonus = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_BONUS, $startDate, $endDate);
        $totalTypePenalty = $this->user->getTotalSalaryAdvancesRequest(SalaryAdvanceRequest::TYPE_PENALTY, $startDate, $endDate);
        
        // Get TYPE_OTHER requests for detailed display
        $otherRequests = $this->user->getSalaryAdvancesRequestByType(SalaryAdvanceRequest::TYPE_OTHER, $startDate, $endDate);
        // dd($otherRequests);
        // Calculate totals from actual data
        $totalBonus = $totalTypeBonus;
        $totalPenalty = $totalTypePenalty;
        $totalOtherDeduction = $totalTypeSalary;
        
        // Calculate total other costs (Bonus + TYPE_OTHER requests)
        $totalOtherCosts = $totalBonus; // Bắt đầu với bonus
        if (!empty($otherRequests)) {
            $totalOtherCosts += $otherRequests->sum('amount'); // Cộng thêm TYPE_OTHER requests
        }
        // dd($totalOtherCosts);
        // Tính BHXH theo cách mới: X% của Y (không phụ thuộc vào lương cơ bản)
        // Kiểm tra xem user có đóng bảo hiểm không
        $insuranceDeduction = 0;
        if ($this->user->shouldPayInsuranceForPeriod($startDate, $endDate)) {
            // Lấy settings từ database và parse decimal
            $insuranceRate = (float) Setting::get('social_insurance_contribution_rate', 10.5);
            $insuranceAmount = (float) Setting::get('social_insurance_contribution_amount', 5500000);
            
            // Tính BHXH: X% của Y (không phụ thuộc vào lương cơ bản)
            $insuranceDeduction = $insuranceAmount * ($insuranceRate / 100);
        }
        
        $totalPaid = 0; // You can calculate actual advance payments here
        
        // Calculate total salary
        // TỔNG LƯƠNG = LƯƠNG CƠ BẢN + PHỤ CẤP CƠM NGÀY
        $totalSalary = $salaryBase + $lunchAllowance;
        
        // Tính T.TIỀN LƯƠNG CÒN LẠI theo công thức:
        // = (TỔNG LƯƠNG + CHI PHÍ KHÁC) - (TIỀN PHẠT + TRỪ BHXH + TIỀN ỨNG)
        $netSalary = ($totalSalary + $totalOtherCosts) - ($totalPenalty + $insuranceDeduction + $totalOtherDeduction);

        $this->salaryData = [
            'salaryBase' => $salaryBase,
            'lunchAllowance' => $lunchAllowance,
            'dailyLunchAllowance' => $dailyLunchAllowance,
            'workingDays' => $workingDays,
            'totalBonus' => $totalBonus,
            'totalPenalty' => $totalPenalty,
            'totalOtherDeduction' => $totalOtherDeduction,
            'insuranceDeduction' => $insuranceDeduction,
            'totalPaid' => $totalPaid,
            'totalSalary' => $totalSalary,
            'netSalary' => $netSalary,
            'otherRequests' => $otherRequests,
            'totalOtherCosts' => $totalOtherCosts
        ];
    }

    public function array(): array
    {
        $data = [];

        // Company header rows
        $data[] = ['CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG', '', '', '', '', '', '', '', '', ''];
        $data[] = ['ĐC: Số 216, tổ 4, ấp 7, Bình Sơn, Long Thành, Đồng Nai', '', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', '']; // Empty row
        $data[] = ['BẢNG LƯƠNG VĂN PHÒNG', '', '', '', '', '', '', '', '', ''];
        $data[] = ['(Tháng ' . $this->month . ')', '', '', '', '', '', '', '', '', ''];
        $data[] = ['HỌ VÀ TÊN: ' . strtoupper($this->user->full_name), '', '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', '']; // Empty row

        // Table headers - THIS IS THE IMPORTANT ROW
        $data[] = [
            '',
            'LƯƠNG CƠ BẢN',
            'PHỤ CẤP CƠM NGÀY',
            'TỔNG LƯƠNG',
            'CHI PHÍ KHÁC',
            'TIỀN PHẠT',
            'TRỪ BHXH',
            'TIỀN ỨNG',
            'T.TIỀN LƯƠNG CÒN LẠI',
            'GHI CHÚ'
        ];

        // Data rows - each row represents a salary component
        $data[] = [
            'LƯƠNG CƠ BẢN',
            number_format($this->salaryData['salaryBase']),
            '',
            number_format($this->salaryData['salaryBase']),
            '',
            '',
            '',
            '',
            '',
            ''
        ];

        // Lunch allowance row
        if ($this->salaryData['lunchAllowance'] > 0) {
            $data[] = [
                'CƠM TRƯA (' . $this->salaryData['workingDays'] . ' X ' . number_format($this->salaryData['dailyLunchAllowance']) . ')',
                '',
                number_format($this->salaryData['lunchAllowance']),
                number_format($this->salaryData['lunchAllowance']),
                '',
                '',
                '',
                '',
                '',
                ''
            ];
        }

        // Bonus row (if any)
        if ($this->salaryData['totalBonus'] > 0) {
            $data[] = [
                'THƯỞNG',
                '',
                '',
                '',
                number_format($this->salaryData['totalBonus']),
                '',
                '',
                '',
                '',
                ''
            ];
        }

        // Penalty row (if any)
        if ($this->salaryData['totalPenalty'] > 0) {
            $data[] = [
                'TIỀN PHẠT',
                '',
                '',
                '',
                '',
                number_format($this->salaryData['totalPenalty']), // Column F: TIỀN PHẠT
                '',
                '',
                '',
                ''
            ];
        }

        // Salary advance row (if any)
        if ($this->salaryData['totalOtherDeduction'] > 0) {
            $data[] = [
                'TIỀN ỨNG',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($this->salaryData['totalOtherDeduction']),
                ''
            ];
        }

        // TYPE_OTHER requests rows (if any)
        if (!empty($this->salaryData['otherRequests'])) {
            foreach ($this->salaryData['otherRequests'] as $otherRequest) {
                $data[] = [
                    $otherRequest->reason, // Column A: Reason
                    '',
                    '',
                    '',
                    number_format($otherRequest->amount), // Column E: Amount
                    '', // Column F: TIỀN PHẠT
                    '',
                    '',
                    '',
                    ''
                ];
            }
        }

        // Totals row
        $data[] = [
            'TỔNG CỘNG',
            number_format($this->salaryData['salaryBase']),
            number_format($this->salaryData['lunchAllowance']),
            number_format($this->salaryData['totalSalary']),
            number_format($this->salaryData['totalOtherCosts']), // Cộng bonus + other costs
            number_format($this->salaryData['totalPenalty']), // Column F: TIỀN PHẠT
            $this->salaryData['insuranceDeduction'] > 0 ? number_format($this->salaryData['insuranceDeduction']) : '-',
            $this->salaryData['totalOtherDeduction'] > 0 ? number_format($this->salaryData['totalOtherDeduction']) : '-',
            number_format($this->salaryData['netSalary']),
            ''
        ];

        return $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35, // NGÀY
            'B' => 20, // LƯƠNG CƠ BẢN
            'C' => 22, // PHỤ CẤP CƠM NGÀY
            'D' => 20, // TỔNG LƯƠNG
            'E' => 20, // CHI PHÍ KHÁC
            'F' => 18, // TIỀN PHẠT
            'G' => 18, // TRỪ BHXH
            'H' => 18, // TIỀN ỨNG
            'I' => 25, // T.TIỀN LƯƠNG CÒN LẠI
            'J' => 22, // GHI CHÚ
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style company header
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        // Style title
        $sheet->getStyle('A4:A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Style employee name
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        // Merge header cells ONLY (not the table header row)
        $sheet->mergeCells('A1:J1'); // Company name
        $sheet->mergeCells('A2:J2'); // Address
        $sheet->mergeCells('A4:J4'); // Title
        $sheet->mergeCells('A5:J5'); // Month
        $sheet->mergeCells('A6:J6'); // Employee name

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->array());
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(4)->setRowHeight(25);
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(25);
                $sheet->getRowDimension(8)->setRowHeight(40); // Header row
                
                // Style header row individually to avoid merging
                foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                    $cell = $col . '8';
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => '000000']
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9EAD3']
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                }
                
                // Style data rows
                for ($row = 9; $row <= $lastRow; $row++) {
                    $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                }
                
                // Style totals row
                $sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC']
                    ]
                ]);
                
                // Align data columns 
                for ($row = 9; $row <= $lastRow; $row++) {
                    $sheet->getStyle('B' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            },
        ];
    }
}

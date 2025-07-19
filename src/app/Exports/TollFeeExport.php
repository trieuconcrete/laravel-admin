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

class TollFeeExport implements WithTitle, WithStyles, ShouldAutoSize
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
        
        return "Bảng kê phí cầu đường {$monthName}";
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
        $companyPhone = Setting::get('company_phone', '0917.712.195');
        
        // Customer information
        $customerName = $this->carRental->customer->name ?? 'CÔNG TY TNHH UZIN METAL';
        $customerTaxCode = $this->carRental->customer->tax_code ?? '3603618391';
        $customerAddress = $this->carRental->customer->address ?? 'Đường Nguyễn Ái Quốc, Khu Công Nghiệp Nhơn Trạch III-Giai đoạn 2, Thị trấn Hiệp Phước, Huyện Nhơn Trạch, Tỉnh Đồng Nai, Việt Nam';
        $customerEmail = $this->carRental->customer->email ?? 'phuong.ntm@uzinmetal.com';
        
        // Set company information (Top Left)
        $sheet->setCellValue('A1', $companyName);
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress);
        $sheet->setCellValue('A3', 'ĐT liên hệ: ' . $companyPhone);
        
        // Add main title
        $sheet->setCellValue('A5', 'BẢNG KÊ PHÍ CẦU ĐƯỜNG THÁNG ' . $monthName);
        $sheet->mergeCells('A5:F5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set customer information (Left)
        $sheet->setCellValue('A6', $customerName);
        $sheet->setCellValue('A7', 'MST: ' . $customerTaxCode);
        $sheet->setCellValue('A8', 'Địa chỉ: ' . $customerAddress);
        $sheet->setCellValue('A9', 'Email: ' . $customerEmail);
        
        // Set report details (Right)
        $sheet->setCellValue('E9', 'ĐVT: VNĐ');
        
        // Format company and customer headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A7:A9')->getFont()->setSize(10);
        $sheet->getStyle('E9')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('E9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Table headers - Row 11-12
        $headers = [
            'STT', 'NGÀY THÁNG', 'DIỄN GIẢI', 'CHI PHÍ BÃI XE', 'TỔNG CHI PHÍ TÍNH VÀO ' . $customerName, 'GHI CHÚ'
        ];
        
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '11', $headers[$index]);
            $sheet->mergeCells($column . '11:' . $column . '12');
        }
        
        // Style the header rows
        $sheet->getStyle('A11:F12')->applyFromArray([
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
        $sheet->getColumnDimension('A')->setWidth(8);   // STT
        $sheet->getColumnDimension('B')->setWidth(15);  // NGÀY THÁNG
        $sheet->getColumnDimension('C')->setWidth(30);  // DIỄN GIẢI
        $sheet->getColumnDimension('D')->setWidth(20);  // CHI PHÍ BÃI XE
        $sheet->getColumnDimension('E')->setWidth(25);  // TỔNG CHI PHÍ TÍNH VÀO UZIN
        $sheet->getColumnDimension('F')->setWidth(20);  // GHI CHÚ
        
        // Collect all toll fees from vehicle logs and group by date
        $tollFeeEntries = [];
        $totalTollFee = 0;
        
        // Group toll fees by date
        $tollFeesByDate = [];
        
        foreach ($this->vehicleLogs as $log) {
            if ($log->tollFees && $log->tollFees->count() > 0) {
                $date = (string) $log->run_date;
                if (!isset($tollFeesByDate[$date])) {
                    $tollFeesByDate[$date] = [
                        'date' => $date,
                        'total_amount' => 0,
                        'fee_count' => 0,
                        'notes' => []
                    ];
                }
                
                foreach ($log->tollFees as $tollFee) {
                    $tollFeesByDate[$date]['total_amount'] += $tollFee->fee_amount;
                    $tollFeesByDate[$date]['fee_count']++;
                    if ($tollFee->notes) {
                        $tollFeesByDate[$date]['notes'][] = $tollFee->notes;
                    }
                }
            }
        }
        
        // Convert grouped data to entries
        foreach ($tollFeesByDate as $date => $data) {
            $tollFeeEntries[] = [
                'date' => $data['date'],
                'description' => 'Phí cầu đường' . ($data['fee_count'] > 1 ? ' (' . $data['fee_count'] . ' trạm)' : ''),
                'amount' => $data['total_amount'],
                'notes' => implode(', ', array_unique($data['notes']))
            ];
            $totalTollFee += $data['total_amount'];
        }
        
        // Add data rows starting from row 13
        $row = 13;
        
        foreach ($tollFeeEntries as $index => $entry) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, Carbon::parse($entry['date'])->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $entry['description']);
            $sheet->setCellValue('D' . $row, ''); // Empty for parking fee
            $sheet->setCellValue('E' . $row, number_format($entry['amount']));
            $sheet->setCellValue('F' . $row, $entry['notes'] ?? '');
            $row++;
        }
        
        // Add summary row
        $summaryRow = $row;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $summaryRow . ':C' . $summaryRow);
        $sheet->setCellValue('D' . $summaryRow, '-');
        $sheet->setCellValue('E' . $summaryRow, number_format($totalTollFee));
        $sheet->setCellValue('F' . $summaryRow, '');
        
        // Style the data rows including summary row
        $dataRange = 'A13:F' . $summaryRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set number formats
        $sheet->getStyle('E13:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment - Center all data columns
        $sheet->getStyle('A13:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B13:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C13:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D13:D' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E13:E' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F13:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':F' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow . ':C' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add note
        $noteRow = $summaryRow + 1;
        $sheet->setCellValue('B' . $noteRow, 'Lưu ý: Phí cầu đường trên đã bao gồm VAT');
        
        // Add signature section
        $signatureRow = $noteRow + 1;
        $currentDate = Carbon::now()->format('d/m/Y');
        $timestamp = strtotime(str_replace('/', '-', $currentDate));
        $currentDate = 'ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
        
        // Customer confirmation (left side)
        $sheet->setCellValue('B' . $signatureRow, 'XÁC NHẬN CỦA KHÁCH HÀNG');
        $sheet->setCellValue('B' . ($signatureRow + 1), $customerName ?? 'CÔNG TY TNHH UZIN METAL');
        
        // Company and date (right side)
        $sheet->setCellValue('E' . $signatureRow, 'Long Thành, ' . $currentDate);
        $sheet->setCellValue('E' . ($signatureRow + 1), $companyName);
        
        // Style signature section
        $sheet->getStyle('B' . $signatureRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . ($signatureRow + 1))->getFont()->setBold(true);
        $sheet->getStyle('E' . $signatureRow)->getFont()->setItalic(true);
        $sheet->getStyle('E' . ($signatureRow + 1))->getFont()->setBold(true);
        
        $sheet->getStyle('B' . $signatureRow . ':B' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E' . $signatureRow . ':E' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        return $sheet;
    }
} 
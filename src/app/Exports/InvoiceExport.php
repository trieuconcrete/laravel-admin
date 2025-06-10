<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class InvoiceExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $customer;
    protected $shipments;
    protected $month;
    protected $taxRate;

    /**
     * @param Customer $customer
     * @param Collection $shipments
     * @param string $month
     * @param float $taxRate
     */
    public function __construct(Customer $customer, Collection $shipments, string $month, float $taxRate = 8.0)
    {
        $this->customer = $customer;
        $this->shipments = $shipments;
        $this->month = $month;
        $this->taxRate = $taxRate;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Format month name in Vietnamese
        $monthDate = \DateTime::createFromFormat('Y-m', $this->month);
        $monthName = $monthDate->format('m/Y');
        
        return "Hóa đơn {$monthName}";
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
        $companyAddress = Setting::get('company_address', 'Số 216, tổ 4, ấp 7, Bình Sơn, Long Thành, Đồng Nai');
        $companyTaxCode = Setting::get('company_tax_code', '3603231556');
        
        // Set company information
        $sheet->setCellValue('A1', $companyName);
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress);
        $sheet->setCellValue('A3', 'MST: ' . $companyTaxCode);
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(11);
        
        // Add invoice title
        $sheet->setCellValue('A5', 'BẢNG KÊ VẬN CHUYỂN');
        $sheet->setCellValue('A6', '(Tháng: ' . $monthName . ')');
        $sheet->mergeCells('A5:H5');
        $sheet->mergeCells('A6:H6');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Customer information
        $sheet->setCellValue('A8', 'Kính gửi: ' . $this->customer->name);
        $sheet->setCellValue('A9', 'Địa chỉ: ' . $this->customer->address);
        $sheet->setCellValue('A10', 'MST: ' . $this->customer->tax_code);
        $sheet->setCellValue('A11', 'Email: ' . $this->customer->email);
        
        // Additional invoice information in column G
        $sheet->setCellValue('H9', 'Hóa Đơn: '. Str::random(10) .'');
        
        // Get first day of the month for the statement reference
        $firstDayOfMonth = date('01-m/Y', strtotime($this->month . '-01'));
        $sheet->setCellValue('H10', 'Bảng kê: VNT' . $firstDayOfMonth);
        
        $sheet->setCellValue('H11', 'ĐVT: VNĐ');
        
        $sheet->getStyle('A8')->getFont()->setBold(true);
        $sheet->getStyle('H9:H11')->getFont()->setBold(true);
        
        // Table headers - Row 13
        $headers = ['STT', 'Ngày', 'Điểm đi', 'Điểm đến', 'Số chuyến', 'Khối lượng (kg)', 'Đơn giá', 'Phụ thu', 'Thành tiền', 'Ghi chú'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '13', $headers[$index]);
        }
        
        // Style the header row
        $sheet->getStyle('A13:J13')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFD9EAD3'], // Light green background
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // STT - narrow
        $sheet->getColumnDimension('B')->setWidth(12); // Ngày
        $sheet->getColumnDimension('C')->setWidth(15); // Điểm đi
        $sheet->getColumnDimension('D')->setWidth(15); // Điểm đến
        $sheet->getColumnDimension('E')->setWidth(10); // Số chuyến
        $sheet->getColumnDimension('F')->setWidth(15); // Khối lượng
        $sheet->getColumnDimension('G')->setWidth(12); // Đơn giá
        $sheet->getColumnDimension('H')->setWidth(12); // Phụ thu
        $sheet->getColumnDimension('I')->setWidth(15); // Thành tiền
        $sheet->getColumnDimension('J')->setWidth(20); // Ghi chú
        
        // Add data rows starting from row 14
        $row = 14;
        $totalAmount = 0;
        
        foreach ($this->shipments as $index => $shipment) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $shipment['departure_time']);
            $sheet->setCellValue('C' . $row, $shipment['origin']);
            $sheet->setCellValue('D' . $row, $shipment['destination']);
            $sheet->setCellValue('E' . $row, $shipment['trip_count']);
            $sheet->setCellValue('F' . $row, $shipment['cargo_weight']);
            $sheet->setCellValue('G' . $row, $shipment['unit_price']);
            $sheet->setCellValue('H' . $row, $shipment['combined_fees'] ?? 0); // Phụ thu
            $sheet->setCellValue('I' . $row, $shipment['total_amount']);
            $sheet->setCellValue('J' . $row, $shipment['notes']);
            
            $totalAmount += $shipment['total_amount'];
            $row++;
        }
        
        // Calculate tax and total
        $taxAmount = $totalAmount * ($this->taxRate / 100);
        $grandTotal = $totalAmount + $taxAmount;
        
        // Add summary rows as part of the table
        $summaryStartRow = $row;
        $sheet->setCellValue('H' . $summaryStartRow, 'TỔNG');
        $sheet->setCellValue('I' . $summaryStartRow, $totalAmount);
        
        $sheet->setCellValue('H' . ($summaryStartRow + 1), 'THUẾ GTGT ' . $this->taxRate . '%');
        $sheet->setCellValue('I' . ($summaryStartRow + 1), $taxAmount);
        
        $sheet->setCellValue('H' . ($summaryStartRow + 2), 'TỔNG CỘNG');
        $sheet->setCellValue('I' . ($summaryStartRow + 2), $grandTotal);
        
        // Style the data rows including summary rows
        $dataRange = 'A14:J' . ($summaryStartRow + 2);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set number formats
        $sheet->getStyle('E14:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G14:I' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A14:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E14:E' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G14:I' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Tax and total already calculated above
        
        // Style summary rows
        $sheet->getStyle('H' . $summaryStartRow . ':H' . ($summaryStartRow + 2))->getFont()->setBold(true);
        $sheet->getStyle('I' . $summaryStartRow . ':I' . ($summaryStartRow + 2))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I' . $summaryStartRow . ':I' . ($summaryStartRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Clear cells A-G in summary rows to make them look like they're part of the table
        for ($i = 0; $i < 3; $i++) {
            for ($col = 'A'; $col < 'H'; $col++) {
                $sheet->setCellValue($col . ($summaryStartRow + $i), '');
            }
        }
        
        // Add signature section
        $signatureRow = $summaryStartRow + 4;
        $currentDate = date('d/m/Y');
        $timestamp = strtotime(str_replace('/', '-', $currentDate)); // convert '27/04/2025' -> '27-04-2025'
        $currentDate = 'ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
        
        // Put location and date on a separate line
        $sheet->setCellValue('G' . $signatureRow, 'Long Thành, ' . $currentDate);
        
        // Put customer name and company name on the next line
        $sheet->setCellValue('B' . ($signatureRow + 2), $this->customer->name);
        $sheet->setCellValue('G' . ($signatureRow + 2), $companyName);
        
        $sheet->getStyle('G' . $signatureRow)->getFont()->setBold(true)->setItalic(true);
        $sheet->getStyle('B' . ($signatureRow + 2))->getFont()->setBold(true);
        $sheet->getStyle('G' . ($signatureRow + 2))->getFont()->setBold(true);
        
        $sheet->getStyle('G' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . ($signatureRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . ($signatureRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        return $sheet;
    }
}

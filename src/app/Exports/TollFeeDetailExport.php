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

class TollFeeDetailExport implements WithTitle, WithStyles, ShouldAutoSize
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
        
        return "Bảng kê hóa đơn điện tử phí cầu đường {$monthName}";
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
        $sheet->setCellValue('A1', 'CÔNG TY TNHH THU PHÍ TỰ ĐỘNG VETC');
        
        // Add main title
        $sheet->setCellValue('A3', 'BẢNG KÊ HOÁ ĐƠN ĐIỆN TỬ');
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add date range
        $firstDay = '01/' . $monthDate->format('m/Y');
        $lastDay = $monthDate->format('t/m/Y');
        $sheet->setCellValue('A4', 'Từ ngày ' . $firstDay . ' đến hết ngày ' . $lastDay);
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set customer information
        $sheet->setCellValue('A6', 'Tên người mua hàng: ' . $customerName);
        $sheet->setCellValue('A7', 'Địa chỉ: ' . $customerAddress);
        $sheet->setCellValue('A8', 'Mã số thuế: ' . $customerTaxCode);
        $sheet->setCellValue('A9', 'Số tài khoản giao thông: E0101651638');
        
        // Format headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A6:A9')->getFont()->setSize(10);
        
        // Table headers - Row 11
        $headers = [
            'STT', 'Số hóa đơn', 'Ngày phát sinh hóa đơn', 'Mã giao dịch', 
            'Biển số xe', 'Nội dung hoá đơn', 'Số tiền chưa thuế', 
            'Số tiền thuế GTGT', 'Số tiền sau thuế'
        ];
        
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($column . '11', $headers[$index]);
        }
        
        // Style the header row
        $sheet->getStyle('A11:I11')->applyFromArray([
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
        $sheet->getColumnDimension('B')->setWidth(15);  // Số hóa đơn
        $sheet->getColumnDimension('C')->setWidth(25);  // Ngày phát sinh hóa đơn
        $sheet->getColumnDimension('D')->setWidth(15);  // Mã giao dịch
        $sheet->getColumnDimension('E')->setWidth(15);  // Biển số xe
        $sheet->getColumnDimension('F')->setWidth(35);  // Nội dung hoá đơn
        $sheet->getColumnDimension('G')->setWidth(15);  // Số tiền chưa thuế
        $sheet->getColumnDimension('H')->setWidth(15);  // Số tiền thuế GTGT
        $sheet->getColumnDimension('I')->setWidth(15);  // Số tiền sau thuế
        
        // Collect all toll fees from vehicle logs (not grouped)
        $tollFeeEntries = [];
        $totalBeforeTax = 0;
        $totalVAT = 0;
        $totalAfterTax = 0;
        $vehiclePlate = $this->getVehiclePlate();
        
        foreach ($this->vehicleLogs as $log) {
            if ($log->tollFees && $log->tollFees->count() > 0) {
                foreach ($log->tollFees as $tollFee) {
                    // Calculate VAT (assuming 8% VAT rate)
                    $vatRate = 0.08;
                    $amountBeforeTax = $tollFee->fee_amount / (1 + $vatRate);
                    $vatAmount = $tollFee->fee_amount - $amountBeforeTax;
                    
                    $tollFeeEntries[] = [
                        'invoice_no' => $this->generateInvoiceNumber(),
                        'date' => $log->run_date,
                        'transaction_code' => $tollFee->transaction_code ?? $this->generateTransactionCode(),
                        'plate_number' => $vehiclePlate,
                        'description' => 'Cước đường bộ xe ' . $vehiclePlate,
                        'amount_before_tax' => $amountBeforeTax,
                        'vat_amount' => $vatAmount,
                        'amount_after_tax' => $tollFee->fee_amount
                    ];
                    
                    $totalBeforeTax += $amountBeforeTax;
                    $totalVAT += $vatAmount;
                    $totalAfterTax += $tollFee->fee_amount;
                }
            }
        }
        
        // Add data rows starting from row 12
        $row = 12;
        
        foreach ($tollFeeEntries as $index => $entry) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $entry['invoice_no']);
            $sheet->setCellValue('C' . $row, Carbon::parse($entry['date'])->format('d/m/Y'));
            $sheet->setCellValue('D' . $row, $entry['transaction_code']);
            $sheet->setCellValue('E' . $row, $entry['plate_number']);
            $sheet->setCellValue('F' . $row, $entry['description']);
            $sheet->setCellValue('G' . $row, number_format($entry['amount_before_tax']));
            $sheet->setCellValue('H' . $row, number_format($entry['vat_amount']));
            $sheet->setCellValue('I' . $row, number_format($entry['amount_after_tax']));
            $row++;
        }
        
        // Add summary row
        $summaryRow = $row;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG THÁNG ' . $monthDate->format('m'));
        $sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
        $sheet->setCellValue('G' . $summaryRow, number_format($totalBeforeTax));
        $sheet->setCellValue('H' . $summaryRow, number_format($totalVAT));
        $sheet->setCellValue('I' . $summaryRow, number_format($totalAfterTax));
        
        // Style the data rows including summary row
        $dataRange = 'A12:I' . $summaryRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set number formats
        $sheet->getStyle('G12:I' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G' . $summaryRow . ':I' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A12:I' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':I' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow . ':F' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G' . $summaryRow . ':I' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add signature section
        $signatureRow = $summaryRow + 2;
        $currentDate = Carbon::now()->format('d/m/Y');
        $timestamp = strtotime(str_replace('/', '-', $currentDate));
        $currentDate = 'ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
        
        // Customer confirmation (left side)
        $sheet->setCellValue('B' . $signatureRow, 'XÁC NHẬN CỦA KHÁCH HÀNG');
        $sheet->setCellValue('B' . ($signatureRow + 1), $customerName ?? 'CÔNG TY TNHH UZIN METAL');
        
        // Company and date (right side)
        $sheet->setCellValue('G' . $signatureRow, 'Long Thành, ' . $currentDate);
        $sheet->mergeCells('G' . $signatureRow . ':I' . $signatureRow);
        $sheet->setCellValue('G' . ($signatureRow + 1), $companyName);
        $sheet->mergeCells('G' . ($signatureRow + 1) . ':I' . ($signatureRow + 1));
        
        // Style signature section
        $sheet->getStyle('B' . $signatureRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . ($signatureRow + 1))->getFont()->setBold(true);
        $sheet->getStyle('G' . $signatureRow)->getFont()->setItalic(true);
        $sheet->getStyle('G' . ($signatureRow + 1))->getFont()->setBold(true);
        
        $sheet->getStyle('B' . $signatureRow . ':B' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G' . $signatureRow . ':I' . $signatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . ($signatureRow + 1) . ':I' . ($signatureRow + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        return $sheet;
    }
    
    /**
     * Get vehicle plate number from car rental
     */
    private function getVehiclePlate()
    {
        $vehicle = $this->carRental->carRentalVehicles->first();
        if ($vehicle && $vehicle->vehicle) {
            return $vehicle->vehicle->plate_number ?? '60K41758T';
        }
        return '60K41758T';
    }
    
    /**
     * Generate a random invoice number
     */
    private function generateInvoiceNumber()
    {
        return rand(24000000, 25000000);
    }
    
    /**
     * Generate a random transaction code
     */
    private function generateTransactionCode()
    {
        return rand(1600000000, 1700000000);
    }
} 
<?php

namespace App\Exports;

use App\Models\CarRental;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class DebtSummaryExport implements WithMultipleSheets, ShouldAutoSize
{
    protected $carRental;
    protected $debtSummary;
    protected $shipments;

    public function __construct(CarRental $carRental, array $debtSummary, $shipments)
    {
        $this->carRental = $carRental;
        $this->debtSummary = $debtSummary;
        $this->shipments = $shipments;
    }

    public function sheets(): array
    {
        return [
            'Tổng kết công nợ' => new DebtSummarySheet($this->carRental, $this->debtSummary),
            'Chi tiết từng chuyến' => new ShipmentDetailSheet($this->shipments, $this->debtSummary['currency']),
        ];
    }
}

class DebtSummarySheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected $carRental;
    protected $debtSummary;

    public function __construct(CarRental $carRental, array $debtSummary)
    {
        $this->carRental = $carRental;
        $this->debtSummary = $debtSummary;
    }

    public function title(): string
    {
        return 'Tổng kết công nợ';
    }

    public function collection()
    {
        // Return empty collection to avoid duplicate data
        // All data will be populated in styles() method
        return collect();
    }

    public function styles(Worksheet $sheet)
    {
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
        
        // Add report title
        $sheet->setCellValue('A5', 'TỔNG KẾT CÔNG NỢ - THUÊ XE');
        $sheet->mergeCells('A5:E5');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Customer information
        $customerName = $this->carRental->customer->name ?? 'N/A';
        $customerAddress = $this->carRental->customer->address ?? 'N/A';
        $customerTaxCode = $this->carRental->customer->tax_code ?? 'N/A';
        
        $sheet->setCellValue('A7', 'Khách hàng: ' . $customerName);
        $sheet->setCellValue('A8', 'Địa chỉ: ' . $customerAddress);
        $sheet->setCellValue('A9', 'MST: ' . $customerTaxCode);
        
        $sheet->getStyle('A7:A9')->getFont()->setBold(true);
        
        // Table headers - Row 11
        $headers = ['THÔNG TIN', 'GIÁ TRỊ', 'ĐƠN VỊ', 'GHI CHÚ', ''];
        $columns = ['A', 'B', 'C', 'D', 'E'];
        
        foreach ($columns as $index => $column) {
            if (isset($headers[$index])) {
                $sheet->setCellValue($column . '11', $headers[$index]);
            }
        }
        
        // Style the header row
        $sheet->getStyle('A11:E11')->applyFromArray([
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
        $this->setColumnWidths($sheet);
        
        // Add data rows starting from row 12
        $row = 12;
        $this->addDataRows($sheet, $row);
        
        // Style the data rows
        $dataRange = 'A11:E' . ($row + 20);
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
        $this->setNumberFormats($sheet, $row);
        
        // Add signature section
        $this->addSignatureSection($sheet, $row + 22, $companyName);
        
        return $sheet;
    }

    /**
     * Set column widths
     */
    protected function setColumnWidths(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(40);  // THÔNG TIN
        $sheet->getColumnDimension('B')->setWidth(20);  // GIÁ TRỊ
        $sheet->getColumnDimension('C')->setWidth(15);  // ĐƠN VỊ
        $sheet->getColumnDimension('D')->setWidth(30);  // GHI CHÚ
        $sheet->getColumnDimension('E')->setWidth(15);  // Empty
    }

    /**
     * Add data rows
     */
    protected function addDataRows(Worksheet $sheet, int $row)
    {
        // Thông tin cơ bản
        $sheet->setCellValue('A' . $row, 'Mã thuê xe:');
        $sheet->setCellValue('B' . $row, $this->carRental->id);
        $sheet->setCellValue('C' . $row, '');
        $sheet->setCellValue('D' . $row, '');
        
        $sheet->setCellValue('A' . ($row + 1), 'Loại thuê xe:');
        $sheet->setCellValue('B' . ($row + 1), $this->getRentalTypeText());
        $sheet->setCellValue('C' . ($row + 1), '');
        $sheet->setCellValue('D' . ($row + 1), '');
        
        $sheet->setCellValue('A' . ($row + 2), 'Trạng thái:');
        $sheet->setCellValue('B' . ($row + 2), $this->getStatusText());
        $sheet->setCellValue('C' . ($row + 2), '');
        $sheet->setCellValue('D' . ($row + 2), '');
        
        $sheet->setCellValue('A' . ($row + 3), 'Ngày bắt đầu:');
        $sheet->setCellValue('B' . ($row + 3), $this->carRental->start_date ? \Carbon\Carbon::parse($this->carRental->start_date)->format('d/m/Y') : 'N/A');
        $sheet->setCellValue('C' . ($row + 3), '');
        $sheet->setCellValue('D' . ($row + 3), '');
        
        $sheet->setCellValue('A' . ($row + 4), 'Ngày kết thúc:');
        $sheet->setCellValue('B' . ($row + 4), $this->carRental->end_date ? \Carbon\Carbon::parse($this->carRental->end_date)->format('d/m/Y') : 'N/A');
        $sheet->setCellValue('C' . ($row + 4), '');
        $sheet->setCellValue('D' . ($row + 4), '');
        
        $sheet->setCellValue('A' . ($row + 5), 'Giờ làm việc kết thúc:');
        $sheet->setCellValue('B' . ($row + 5), $this->carRental->end_working_hour ? \Carbon\Carbon::parse($this->carRental->end_working_hour)->format('H:i') : '17:30');
        $sheet->setCellValue('C' . ($row + 5), '');
        $sheet->setCellValue('D' . ($row + 5), '');
        
        // Tổng kết công nợ
        $sheet->setCellValue('A' . ($row + 7), 'TỔNG KẾT CÔNG NỢ');
        $sheet->setCellValue('B' . ($row + 7), '');
        $sheet->setCellValue('C' . ($row + 7), '');
        $sheet->setCellValue('D' . ($row + 7), '');
        $sheet->getStyle('A' . ($row + 7))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 7))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
        
        $sheet->setCellValue('A' . ($row + 8), 'Phí thuê xe theo tháng:');
        $sheet->setCellValue('B' . ($row + 8), $this->debtSummary['monthly_rental_fee'] ?? 0);
        $sheet->setCellValue('C' . ($row + 8), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 8), '');
        
        $sheet->setCellValue('A' . ($row + 9), 'Tổng chi phí tăng ca:');
        $sheet->setCellValue('B' . ($row + 9), $this->debtSummary['total_overtime_cost'] ?? 0);
        $sheet->setCellValue('C' . ($row + 9), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 9), '');
        
        $sheet->setCellValue('A' . ($row + 10), 'Tổng phí cầu đường:');
        $sheet->setCellValue('B' . ($row + 10), $this->debtSummary['total_toll_fees'] ?? 0);
        $sheet->setCellValue('C' . ($row + 10), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 10), '');
        
        $sheet->setCellValue('A' . ($row + 11), 'Tổng phí đỗ xe:');
        $sheet->setCellValue('B' . ($row + 11), $this->debtSummary['total_parking_fees'] ?? 0);
        $sheet->setCellValue('C' . ($row + 11), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 11), '');
        
        $sheet->setCellValue('A' . ($row + 12), 'Tổng quãng đường:');
        $sheet->setCellValue('B' . ($row + 12), $this->debtSummary['total_distance'] ?? 0);
        $sheet->setCellValue('C' . ($row + 12), 'km');
        $sheet->setCellValue('D' . ($row + 12), '');
        
        $sheet->setCellValue('A' . ($row + 13), 'Phí vượt quãng đường:');
        $sheet->setCellValue('B' . ($row + 13), $this->debtSummary['over_distance_fee'] ?? 0);
        $sheet->setCellValue('C' . ($row + 13), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 13), '');
        
        // Tổng cộng
        $sheet->setCellValue('A' . ($row + 15), 'Tổng cộng (chưa VAT):');
        $sheet->setCellValue('B' . ($row + 15), $this->debtSummary['subtotal'] ?? 0);
        $sheet->setCellValue('C' . ($row + 15), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 15), '');
        $sheet->getStyle('A' . ($row + 15))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 15))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');
        
        $sheet->setCellValue('A' . ($row + 16), 'Thuế VAT (' . ($this->debtSummary['vat_rate'] ?? 8) . '%):');
        $sheet->setCellValue('B' . ($row + 16), $this->debtSummary['vat_amount'] ?? 0);
        $sheet->setCellValue('C' . ($row + 16), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 16), '');
        
        $sheet->setCellValue('A' . ($row + 17), 'Tổng cộng (có VAT):');
        $sheet->setCellValue('B' . ($row + 17), $this->debtSummary['total_with_vat'] ?? 0);
        $sheet->setCellValue('C' . ($row + 17), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 17), '');
        $sheet->getStyle('A' . ($row + 17))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 17))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1ECF1');
        
        $sheet->setCellValue('A' . ($row + 18), 'Đã thanh toán:');
        $sheet->setCellValue('B' . ($row + 18), $this->debtSummary['paid_amount'] ?? 0);
        $sheet->setCellValue('C' . ($row + 18), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 18), '');
        
        $sheet->setCellValue('A' . ($row + 19), 'Còn nợ:');
        $sheet->setCellValue('B' . ($row + 19), $this->debtSummary['remaining_debt'] ?? 0);
        $sheet->setCellValue('C' . ($row + 19), $this->debtSummary['currency'] ?? 'VND');
        $sheet->setCellValue('D' . ($row + 19), '');
        $sheet->getStyle('A' . ($row + 19))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 19))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');
        
        $sheet->setCellValue('A' . ($row + 20), 'Cập nhật lần cuối:');
        $sheet->setCellValue('B' . ($row + 20), $this->debtSummary['calculation_date'] ?? now()->format('d/m/Y H:i'));
        $sheet->setCellValue('C' . ($row + 20), '');
        $sheet->setCellValue('D' . ($row + 20), '');
        
        // Add empty row for spacing
        $sheet->setCellValue('A' . ($row + 21), '');
        $sheet->setCellValue('B' . ($row + 21), '');
        $sheet->setCellValue('C' . ($row + 21), '');
        $sheet->setCellValue('D' . ($row + 21), '');
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        // Format số tiền
        $sheet->getStyle('B' . $row . ':B' . ($row + 21))->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A' . $row . ':A' . ($row + 21))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $row . ':B' . ($row + 21))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C' . $row . ':C' . ($row + 21))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row . ':D' . ($row + 21))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    /**
     * Add signature section
     */
    protected function addSignatureSection(Worksheet $sheet, int $startRow, string $companyName)
    {
        $currentDate = date('d/m/Y');
        $timestamp = strtotime(str_replace('/', '-', $currentDate));
        $currentDate = 'ngày ' . date('d', $timestamp) . ' tháng ' . date('m', $timestamp) . ' năm ' . date('Y', $timestamp);
        
        // Put location and date
        $sheet->setCellValue('D' . $startRow, 'Long Thành, ' . $currentDate);
        
        // Put customer name and company name
        $customerName = $this->carRental->customer->name ?? 'N/A';
        $sheet->setCellValue('A' . ($startRow + 2), $customerName);
        $sheet->setCellValue('D' . ($startRow + 2), $companyName);
        
        $sheet->getStyle('D' . $startRow)->getFont()->setBold(true)->setItalic(true);
        $sheet->getStyle('A' . ($startRow + 2))->getFont()->setBold(true);
        $sheet->getStyle('D' . ($startRow + 2))->getFont()->setBold(true);
        
        $sheet->getStyle('D' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function getRentalTypeText(): string
    {
        return match($this->carRental->type) {
            1 => 'Thuê nguyên xe tính theo chuyến',
            2 => 'Thuê xe theo kiểu khoáng',
            default => 'N/A'
        };
    }

    private function getStatusText(): string
    {
        return match($this->carRental->type) {
            1 => 'Hoạt động',
            2 => 'Tạm dừng',
            3 => 'Kết thúc',
            default => 'N/A'
        };
    }
}

class ShipmentDetailSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected $shipments;
    protected $currency;

    public function __construct($shipments, string $currency)
    {
        $this->shipments = $shipments;
        $this->currency = $currency;
    }

    public function title(): string
    {
        return 'Chi tiết từng chuyến';
    }

    public function collection()
    {
        $data = collect();

        // Header
        $data->push([
            'Ngày chạy',
            'Biển số xe',
            'Tài xế',
            'Quãng đường (km)',
            'Giờ bắt đầu',
            'Giờ kết thúc',
            'Tăng ca (giờ)',
            'Chi phí tăng ca',
            'Phí cầu đường',
            'Phí đỗ xe',
            'Tổng chuyến'
        ]);

        // Dữ liệu từng chuyến
        foreach ($this->shipments as $shipment) {
            $data->push([
                \Carbon\Carbon::parse($shipment->run_date)->format('d/m/Y'),
                $shipment->vehicle->plate_number ?? 'N/A',
                $shipment->driver->full_name ?? 'N/A',
                $shipment->distance ?? 0,
                $shipment->start_time ? \Carbon\Carbon::parse($shipment->start_time)->format('H:i') : 'N/A',
                $shipment->end_time ? \Carbon\Carbon::parse($shipment->end_time)->format('H:i') : 'N/A',
                $shipment->overtime_hours ?? 0,
                $shipment->overtime_cost ?? 0,
                $shipment->tollFees->sum('fee_amount') ?? 0,
                $shipment->parking_fee ?? 0,
                ($shipment->overtime_cost ?? 0) + ($shipment->tollFees->sum('fee_amount') ?? 0) + ($shipment->parking_fee ?? 0)
            ]);
        }

        // Tổng cộng
        $data->push(['', '', '', '', '', '', '', '', '', '', '']);
        $data->push([
            'TỔNG CỘNG',
            '',
            '',
            $this->shipments->sum('distance') ?? 0,
            '',
            '',
            $this->shipments->sum('overtime_hours') ?? 0,
            $this->shipments->sum('overtime_cost') ?? 0,
            $this->shipments->sum(function($s) { return $s->tollFees->sum('fee_amount'); }) ?? 0,
            $this->shipments->sum('parking_fee') ?? 0,
            $this->shipments->sum(function($s) { 
                return ($s->overtime_cost ?? 0) + ($s->tollFees->sum('fee_amount') ?? 0) + ($s->parking_fee ?? 0); 
            })
        ]);

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Style cho header
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Style cho dòng tổng cộng
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:K{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
        ]);

        // Style cho cột số liệu
        $sheet->getStyle('D:D')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sheet->getStyle('G:K')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        // Border cho toàn bộ bảng
        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        return $sheet;
    }
} 
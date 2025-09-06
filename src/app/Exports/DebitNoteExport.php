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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DebitNoteExport implements WithMultipleSheets, ShouldAutoSize
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
            'DEBIT_NOTE' => new DebitNoteSheet($this->carRental, $this->debtSummary),
            'BANG_GIA_DICH_VU' => new PriceListSheet($this->carRental),
        ];
    }
}

class DebitNoteSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
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
        return 'DEBIT_NOTE';
    }

    public function collection()
    {
        // Return empty collection since we're using styles() method to populate data
        return collect();
    }

    public function styles(Worksheet $sheet)
    {
        // Table headers - Row 12
        $headers = [
            'A' => 'STT',
            'B' => 'TÊN HÀNG',
            'C' => 'ĐƠN VỊ TÍNH',
            'D' => 'SỐ LƯỢNG',
            'E' => 'ĐƠN GIÁ',
            'F' => 'THÀNH TIỀN',
            'G' => 'GHI CHÚ',
        ];

        $firstColumn = array_key_first($headers);
        $lastColumn = array_key_last($headers);
        $beforeLastColumn = array_keys($headers)[count($headers) - 2];
        
        // Company information - Header
        $companyName = Setting::get('company_name', 'CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG');
        $companyAddress = Setting::get('company_address', 'Số 216, tổ 4, ấp 7, Bình Sơn, Long Thành, Đồng Nai');
        $companyTaxCode = Setting::get('company_tax_code', '3603231556');
        
        // Set company information
        $sheet->setCellValue('A1', $companyName);
        $sheet->getRowDimension(1)->setRowHeight(20); // Height in points (you can adjust this value)
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress);
        $sheet->setCellValue('A3', 'MST: ' . $companyTaxCode);
        $sheet->mergeCells('A1' . ':' . $lastColumn . '1');
        $sheet->mergeCells('A2' . ':' . $lastColumn . '2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('A4:C4');
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(11);
        
        // Add report title
        $sheet->setCellValue('A5', 'PHIẾU ĐỀ NGHỊ THANH TOÁN (DEBIT NOTE)');
        $sheet->getRowDimension(5)->setRowHeight(25); // Height in points (you can adjust this value)
        $sheet->setCellValue('A6', 'Hợp đồng số: ' . ($this->carRental->contract_number ?? '01/01/2025/HĐNTVCHH/TBS-' . $this->carRental->id));
        $sheet->mergeCells('A5:G5');
        $sheet->mergeCells('A6:G6');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        
        // Customer information
        $customerName = $this->carRental->customer->name ?? 'N/A';
        $customerAddress = $this->carRental->customer->address ?? 'N/A';
        $customerTaxCode = $this->carRental->customer->tax_code ?? 'N/A';
        
        $sheet->setCellValue('A8', 'Bên sử dụng dịch vụ(Service user): ' . $customerName);
        $sheet->setCellValue('A9', 'Địa chỉ(Address): ' . $customerAddress);
        $sheet->setCellValue('A10', 'MST(Tax code): ' . $customerTaxCode);
        $sheet->mergeCells('A8:D8');
        $sheet->mergeCells('A10:G10');
        $sheet->mergeCells('A9:D9');

        $sheet->getStyle('A8')->getFont()->setBold(true);
        
        foreach ($headers as $column => $title) {
            $sheet->setCellValue($column . '12', $title);
        }
        
        // Style the header row
        $sheet->getStyle('A12:G12')->applyFromArray([
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
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Set column widths
        $this->setColumnWidths($sheet);
        
        // Add data rows starting from row 13
        $row = 13;
        $this->addDataRow($sheet, $row);
        
        // Add summary rows
        $summaryRow = $row + 1;
        $this->addSummaryRows($sheet, $summaryRow);
        
        // Style the data rows including summary rows
        $dataRange = 'A13:G' . ($summaryRow + 2);
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
        $this->addSignatureSection($sheet, $summaryRow + 4, $companyName, $customerName);
        
        return $sheet;
    }

    /**
     * Set column widths
     */
    protected function setColumnWidths(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(5);   // STT
        $sheet->getColumnDimension('B')->setWidth(50);  // TÊN HÀNG
        $sheet->getColumnDimension('C')->setWidth(20);  // ĐƠN VỊ TÍNH
        $sheet->getColumnDimension('D')->setWidth(15);  // SỐ LƯỢNG
        $sheet->getColumnDimension('E')->setWidth(20);  // ĐƠN GIÁ
        $sheet->getColumnDimension('F')->setWidth(20);  // THÀNH TIỀN
        $sheet->getColumnDimension('G')->setWidth(30);  // GHI CHÚ
    }

    /**
     * Add data row
     */
    protected function addDataRow(Worksheet $sheet, int $row)
    {
        $startDate = $this->debtSummary['filter_start_date'] ?? $this->carRental->start_date;
        $endDate = $this->debtSummary['filter_end_date'] ?? $this->carRental->end_date;
        
        $serviceName = $this->getServiceName();
        $unit = $this->getUnit();
        $quantity = $this->calculateQuantity($startDate, $endDate);
        
        // Với type = 2 (Thuê xe theo kiểu khoáng), sử dụng monthly_rental_fee
        // Với type = 1 (Thuê nguyên xe tính theo chuyến), sử dụng tổng chi phí thực tế
        if ($this->carRental->type == 2) {
            $unitPrice = $this->debtSummary['monthly_rental_fee'] ?? 0;
            $totalAmount = $quantity * $unitPrice;
        } else {
            // Tính tổng chi phí thực tế từ shipments
            $totalAmount = $this->debtSummary['total_with_vat'] ?? 0;
            $unitPrice = $totalAmount; // Đơn giá = tổng tiền
            $quantity = 1; // Số lượng = 1 chuyến
        }
        
        $sheet->setCellValue('A' . $row, 1);
        $sheet->setCellValue('B' . $row, $serviceName);
        $sheet->setCellValue('C' . $row, $unit);
        $sheet->setCellValue('D' . $row, $quantity);
        $sheet->setCellValue('E' . $row, $unitPrice);
        $sheet->setCellValue('F' . $row, $totalAmount);
        $sheet->setCellValue('G' . $row, 'TỪ ' . ($startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'N/A') . ' ĐẾN ' . ($endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'N/A'));
    }

    /**
     * Add summary rows
     */
    protected function addSummaryRows(Worksheet $sheet, int $row)
    {
        $startDate = $this->debtSummary['filter_start_date'] ?? $this->carRental->start_date;
        $endDate = $this->debtSummary['filter_end_date'] ?? $this->carRental->end_date;
        
        if ($this->carRental->type == 2) {
            $unitPrice = $this->debtSummary['monthly_rental_fee'] ?? 0;
            $quantity = $this->calculateQuantity($startDate, $endDate);
            $totalAmount = $quantity * $unitPrice;
        } else {
            $totalAmount = $this->debtSummary['total_with_vat'] ?? 0;
        }
        
        // Tổng cộng
        $sheet->setCellValue('A' . $row, 'TỔNG');
        $sheet->getStyle('A' . ($row))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->setCellValue('F' . $row, $totalAmount);
        
        // VAT
        $vatRate = $this->debtSummary['vat_rate'] ?? 8; // Default 8%
        $vatAmount = $totalAmount * ($vatRate / 100);
        $sheet->setCellValue('A' . ($row + 1), 'VAT ' . $vatRate . '%');
        $sheet->getStyle('A' . ($row + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . ($row + 1) . ':E' . ($row + 1));
        $sheet->setCellValue('F' . ($row + 1), $vatAmount);
        
        // Tổng tiền
        $grandTotal = $totalAmount + $vatAmount;
        $sheet->setCellValue('A' . ($row + 2), 'TỔNG TIỀN');
        $sheet->getStyle('A' . ($row + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('A' . ($row + 2) . ':E' . ($row + 2));
        $sheet->setCellValue('F' . ($row + 2), $grandTotal);
        
        // Style summary rows
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 1) . ':F' . ($row + 1))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 2) . ':F' . ($row + 2))->getFont()->setBold(true);
        
        $sheet->getStyle('F' . $row . ':F' . ($row + 2))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('F' . $row . ':F' . ($row + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    /**
     * Add signature section
     */
    protected function addSignatureSection(Worksheet $sheet, int $startRow, string $companyName, string $customerName)
    {
        // Thông tin thanh toán
        $sheet->setCellValue('A' . $startRow, 'Vui lòng thanh toán tiền vào tài khoản bên dưới');
        $sheet->mergeCells('A' . $startRow . ':G' . $startRow);
        $sheet->setCellValue('A' . ($startRow + 1), '- Công ty TNHH MTV Vận Tải Hoàng Phú Long');
        $sheet->mergeCells('A' . ($startRow + 1) . ':G' . ($startRow + 1));
        $sheet->setCellValue('A' . ($startRow + 2), '- Tài khoản số : 0401 001 392 365');
        $sheet->mergeCells('A' . ($startRow + 2) . ':G' . ($startRow + 2));
        $sheet->setCellValue('A' . ($startRow + 3), '- Tại ngân hàng VCB – PGD Lộc An Chi Nhánh Nhơn Trạch');
        $sheet->mergeCells('A' . ($startRow + 3) . ':G' . ($startRow + 3));
        
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . ($startRow + 1) . ':A' . ($startRow + 3))->getFont()->setBold(true);
        
        // Ngày lập
        $currentDate = now()->format('d/m/Y');
        $sheet->setCellValue('F' . ($startRow + 5), 'Long Thành, ngày ' . $currentDate);
        $sheet->mergeCells('F' . ($startRow + 5) . ':G' . ($startRow + 5));
        $sheet->getStyle('F' . ($startRow + 5))->getFont()->setBold(true);
        
        // Chữ ký
        $sheet->setCellValue('A' . ($startRow + 7), $customerName);
        $sheet->setCellValue('E' . ($startRow + 7), $companyName);
        $sheet->mergeCells('A' . ($startRow + 7) . ':C' . ($startRow + 7));
        $sheet->mergeCells('E' . ($startRow + 7) . ':G' . ($startRow + 7));
        
        $sheet->getStyle('A' . ($startRow + 7) . ':E' . ($startRow + 7))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($startRow + 7) . ':E' . ($startRow + 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function getServiceName(): string
    {
        return match($this->carRental->type) {
            1 => 'THUÊ NGUYÊN XE TÍNH THEO CHUYẾN',
            2 => 'THUÊ XE THEO KIỂU KHOÁNG',
            default => 'DỊCH VỤ THUÊ XE'
        };
    }

    private function getUnit(): string
    {
        return match($this->carRental->type) {
            1 => 'CHUYẾN',
            2 => 'THÁNG',
            default => 'ĐƠN VỊ'
        };
    }

    private function calculateQuantity($startDate, $endDate): int
    {
        if (!$startDate || !$endDate) {
            return 1;
        }
        
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        if ($this->carRental->type == 2) { // Thuê xe theo kiểu khoáng
            // Tính số tháng
            return $start->diffInMonths($end) + 1;
        } else {
            // Thuê nguyên xe tính theo chuyến
            return 1;
        }
    }
}

class PriceListSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected $carRental;

    public function __construct(CarRental $carRental)
    {
        $this->carRental = $carRental;
    }

    public function title(): string
    {
        return 'BANG_GIA_DICH_VU';
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
        $sheet->setCellValue('A1', $companyName . ' / 皇富龙运输一成员责任有限公司');
        $sheet->setCellValue('A2', 'ĐC: ' . $companyAddress . ' / 地址：同奈省，隆城县，平山社，7邑，4组，216号');
        $sheet->setCellValue('A3', 'MST: ' . $companyTaxCode);
        $sheet->setCellValue('A4', 'Điện thoại: 0917.712.195');
        
        // Format company header
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:A4')->getFont()->setSize(11);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Add report title
        $sheet->setCellValue('A6', 'BẢNG GIÁ DỊCH VỤ VẬN CHUYỂN HÀNG HÓA / 货物运输服务报价单');
        $sheet->mergeCells('A6:G6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Customer information
        $customerName = $this->carRental->customer->name ?? 'N/A';
        $customerAddress = $this->carRental->customer->address ?? 'N/A';
        $customerTaxCode = $this->carRental->customer->tax_code ?? 'N/A';
        
        $sheet->setCellValue('A8', 'Kính gửi: ' . $customerName . ' / 敬致：' . $customerName);
        $sheet->setCellValue('A9', 'Địa chỉ: ' . $customerAddress . ' / 地址：' . $customerAddress);
        $sheet->setCellValue('A10', 'MST: ' . $customerTaxCode);
        
        $sheet->getStyle('A8:A10')->getFont()->setBold(true);
        $sheet->mergeCells('A8:G8');
        $sheet->mergeCells('A9:G9');
        $sheet->mergeCells('A10:G10');
        
        // Company introduction
        $sheet->setCellValue('A12', 'Công ty chúng tôi chuyên kinh doanh về lĩnh vực vận chuyển hàng hóa. / 我公司专业从事货运代理领域。');
        $sheet->setCellValue('A13', 'Lời đầu tiên chúng tôi xin gởi đến Qúy khách hàng lời chào trân trọng và lời chúc sức khỏe. / 首先，我们谨向尊贵的客户致以问候和最良好的健康祝愿。');
        $sheet->setCellValue('A14', 'Chúng tôi xin trân trọng gởi đến Qúy Khách hàng Bảng báo giá như sau: / 我司向贵司发报价单如下：');
        
        $sheet->mergeCells('A12:G12');
        $sheet->mergeCells('A13:G13');
        $sheet->mergeCells('A14:G14');
        
        // Table headers - Row 16
        $headers = ['STT序号', 'NƠI ĐI出发地', 'NƠI ĐẾN 目的地', '4趟一下DƯỚI 4 CHUYỀN', '5趟以上TỪ 5 CHUYẾN TRỞ LÊN', '月THÁNG', '备注GHI CHÚ'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        
        foreach ($columns as $index => $column) {
            if (isset($headers[$index])) {
                $sheet->setCellValue($column . '16', $headers[$index]);
            }
        }
        
        // Style the header row
        $sheet->getStyle('A16:G16')->applyFromArray([
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
        
        // Add data rows starting from row 17
        $row = 17;
        $this->addDataRow($sheet, $row);
        
        // Add notes section
        $this->addNotesSection($sheet, $row + 2);
        
        // Style the data rows including notes
        $dataRange = 'A16:G' . ($row + 8);
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
        
        return $sheet;
    }

    /**
     * Set column widths
     */
    protected function setColumnWidths(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(8);   // STT序号 - reduced from 15 to 8
        $sheet->getColumnDimension('B')->setWidth(50);  // NƠI ĐI出发地
        $sheet->getColumnDimension('C')->setWidth(20);  // NƠI ĐẾN 目的地
        $sheet->getColumnDimension('D')->setWidth(20);  // 4趟一下DƯỚI 4 CHUYỀN
        $sheet->getColumnDimension('E')->setWidth(20);  // 5趟以上TỪ 5 CHUYẾN TRỞ LÊN
        $sheet->getColumnDimension('F')->setWidth(20);  // 月THÁNG
        $sheet->getColumnDimension('G')->setWidth(20);  // 备注GHI CHÚ
    }

    /**
     * Add data row
     */
    protected function addDataRow(Worksheet $sheet, int $row)
    {
        $sheet->setCellValue('A' . $row, 1);
        $sheet->setCellValue('B' . $row, 'TĂNG BO TRONG NHÀ XƯỞNG周转货物');
        $sheet->setCellValue('C' . $row, '450.000 VNĐ/C越南盾/一趟');
        $sheet->setCellValue('D' . $row, '350.000 VNĐ/C越南盾/一趟');
        $sheet->setCellValue('E' . $row, '46.500.000VND');
        $sheet->setCellValue('F' . $row, '5顿货车XE 5 TẤN');
        $sheet->setCellValue('G' . $row, '');
    }

    /**
     * Add notes section
     */
    protected function addNotesSection(Worksheet $sheet, int $startRow)
    {
        $sheet->setCellValue('A' . $startRow, 'Ghi chú备注:');
        $sheet->mergeCells('A' . $startRow . ':G' . $startRow);
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);
        
        $sheet->setCellValue('A' . ($startRow + 1), '- Giá trên chưa bao gồm VAT / - 以上价格不含增值税');
        $sheet->setCellValue('A' . ($startRow + 2), '- Giá có thể thay đổi tùy theo thị trường / - 价格可能根据市场情况变化');
        $sheet->setCellValue('A' . ($startRow + 3), '- Thời gian giao hàng: Theo thỏa thuận / - 交货时间：按约定');
        
        $sheet->mergeCells('A' . ($startRow + 1) . ':G' . ($startRow + 1));
        $sheet->mergeCells('A' . ($startRow + 2) . ':G' . ($startRow + 2));
        $sheet->mergeCells('A' . ($startRow + 3) . ':G' . ($startRow + 3));
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        // Set text alignment
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
} 
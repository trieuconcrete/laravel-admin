<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class TopbandShipmentExport extends BaseShipmentExport
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bảng kê vận chuyển ' . date('m/Y', strtotime($this->startDate));
    }

    /**
     * @return string
     */
    protected function getReportTitle(): string
    {
        return mb_strtoupper(sprintf(
    'BẢNG KÊ VẬN CHUYỂN %s THÁNG %s NĂM %s',
    $this->customer->name,
            date('m', strtotime($this->startDate)),
            date('Y', strtotime($this->startDate))
        ), 'UTF-8');
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'A' => 'STT',
            'B' => 'Ngày',
            'C' => 'Số xe',
            'D' => 'Điểm đi',
            'E' => 'Điểm đến',
            'F' => 'Số tấn',
            'G' => 'Địa chỉ',
            'H' => 'Loại hàng',
            'I' => 'TTGT',
            'J' => 'Đơn giá',
            'K' => 'Phụ thu kết hợp',
            'L' => 'Phí bốc xếp',
            'M' => 'Thành tiền',
            'N' => 'Ghi chú'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set headers font
        $this->setDefaultFont($sheet, 13); // Row 13 là header row
        $this->setUpHeaderAndFooter($sheet);
        // Table headers - Row 6
        $headers = $this->getHeaders();
        foreach ($headers as $column => $title) {
            $sheet->setCellValue($column . '6', $title);
        }
        // Style the header row
        $firstColumn = array_key_first($headers);
        $lastColumn = array_key_last($headers);
        $sheet->getStyle($firstColumn . '6:' . $lastColumn . '6')->applyFromArray([
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

        // Add data rows starting from row 7
        $row = 7;
        $totalAmount = 0;
        foreach ($this->shipments as $index => $shipment) {
            $this->addDataRow($sheet, $row, $index, $shipment);
            $totalAmount += $shipment['total_amount'];
            $row++;
        }

        $this->addDataTotalRow($sheet, $row, $totalAmount);
        
        // Set number formats
        $this->setNumberFormats($sheet, $row);
        
        // Add signature section
        $this->addSignatureSection($sheet, $row + 4, $this->companyName);
        
        return $sheet;
    }

    /**
     * Summary of addDataRow
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $row
     * @param int $index
     * @param array $shipment
     * @return void
     */
    protected function addDataRow(Worksheet $sheet, int $row, int $index, array $shipment)
    {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $shipment['departure_time']); // Ngày
        $sheet->setCellValue('C' . $row, $shipment['plate_number'] ?? ''); // Số xe
        $sheet->setCellValue('D' . $row, $shipment['origin']); // Điểm đi
        $sheet->setCellValue('E' . $row, $shipment['destination']); // Điểm đến
        $sheet->setCellValue('F' . $row, $shipment['cargo_weight'] ?? ''); // Số tấn
        $sheet->setCellValue('G' . $row, $shipment['company'] ?? ''); // Địa chỉ
        $sheet->setCellValue('H' . $row, $shipment['goods_name'] ?? ''); // Loại hàng
        $sheet->setCellValue('I' . $row, ''); // TTGT
        $sheet->setCellValue('J' . $row, $shipment['unit_price'] ?? 0); // Đơn giá
        $sheet->setCellValue('K' . $row, $shipment['total_combined_surcharge'] ?? 0); // Phụ thu kết hợp
        $sheet->setCellValue('L' . $row, $shipment['total_combined_cargo_handling'] ?? 0); // Phí bốc xếp
        $sheet->setCellValue('M' . $row, $shipment['total_amount']); // Thành tiền
        $sheet->setCellValue('N' . $row, $shipment['notes'] ?? ''); // Ghi chú
    }

    /**
     * Summary of addDataTotalRow
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $row
     * @param float $totalAmount
     * @return void
     */
    protected function addDataTotalRow(Worksheet $sheet, int $row, float $totalAmount)
    {
        // Add summary row
        $summaryRow = $row;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue($this->beforeLastColumn . $summaryRow, $totalAmount);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':' . $this->lastColumn . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // VAT
        $summaryRow++;
        $vatAmount = $totalAmount * 0.08; // Assuming 8% VAT
        $sheet->setCellValue('A' . $summaryRow, 'THUẾ GTGT 8%');
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue($this->beforeLastColumn . $summaryRow, $vatAmount);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':' . $this->lastColumn . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Amount + VAT
        $summaryRow++;
        $totalAmountVAT = $totalAmount + $vatAmount;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG THANH TOÁN');
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue($this->beforeLastColumn . $summaryRow, $totalAmountVAT);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':' . $this->lastColumn . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->beforeLastColumn . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Style the data rows including summary row
        $dataRange = 'A7:' . $this->lastColumn . $summaryRow;
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
    }

    /**
     * Set column widths
     */
    protected function setColumnWidths(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(5);   // STT
        $sheet->getColumnDimension('B')->setWidth(15);  // Ngày
        $sheet->getColumnDimension('C')->setWidth(12);  // Số xe
        $sheet->getColumnDimension('D')->setWidth(15);  // Điểm đi
        $sheet->getColumnDimension('E')->setWidth(15);  // Điểm đến
        $sheet->getColumnDimension('F')->setWidth(12);  // Số tấn
        $sheet->getColumnDimension('G')->setWidth(15);  // Địa chỉ
        $sheet->getColumnDimension('H')->setWidth(20);  // Loại hàng
        $sheet->getColumnDimension('I')->setWidth(15);  // TTGT
        $sheet->getColumnDimension('J')->setWidth(15);  // Đơn giá
        $sheet->getColumnDimension('K')->setWidth(15);  // Phụ thu kết hợp
        $sheet->getColumnDimension('L')->setWidth(15);  // Phí bốc xếp
        $sheet->getColumnDimension('M')->setWidth(15);  // Thành tiền
        $sheet->getColumnDimension('N')->setWidth(20);  // Ghi chú
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        // $sheet->getStyle('F7:F' . ($row - 1))->getNumberFormat()->setFormatCode('#.#0');
        $sheet->getStyle('J7' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K7' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('L7' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('M7' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        // Set text alignment
        $sheet->getStyle('A1:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A7:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B7:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C7:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F7:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    /**
     * Summary of setUpHeaderAndFooter
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function setUpHeaderAndFooter(Worksheet $sheet): void
    {
        // Set no border for A1:M5
        // $sheet->getStyle('A1:M5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        // Set white border with white background for A1:M5
        $sheet->getStyle('A1:N5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:N5')->getBorders()->getAllBorders()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:N5')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:N5')->getFill()->getStartColor()->setRGB('FFFFFF');
        $sheet->getStyle('O1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);

        // Set company information
        $sheet->setCellValue('A1', mb_strtoupper($this->companyName, 'UTF-8'));
        $sheet->setCellValue('A2', 'Địa chỉ : ' . $this->companyAddress);
        $sheet->setCellValue('A3', "Điện thoại : {$this->companyPhone}            Email : {$this->companyEmail}");
        $sheet->mergeCells('A1' . ':' . $this->beforeLastColumn . '1');
        $sheet->mergeCells('A2' . ':' . $this->beforeLastColumn . '2');
        $sheet->mergeCells('A3' . ':' . $this->beforeLastColumn . '3');
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20);
        $sheet->getRowDimension(1)->setRowHeight(44); // Height in points (you can adjust this value)
        $sheet->getStyle('A2:A3')->getFont()->setSize(14);
        
        // Add report title
        $sheet->setCellValue('A4', $this->getReportTitle());
        $sheet->getRowDimension(4)->setRowHeight(25); // Height in points (you can adjust this value)
        $sheet->getRowDimension(5)->setRowHeight(25); // Height in points (you can adjust this value)
        $sheet->setCellValue('A5', 'ĐÍNH KÈM HÓA ĐƠN SỐ :           ' . '   NGÀY : ');
        
        $sheet->mergeCells('A4' . ':' . $this->beforeLastColumn . '4');
        $sheet->mergeCells('A5' . ':' . $this->beforeLastColumn . '5');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(15);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(15);
        $sheet->getStyle('A4:A5')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
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
        $sheet->setCellValue('G' . $startRow, 'Long Thành, ' . $currentDate);
        
        $sheet->mergeCells("G{$startRow}" . ':' . $this->beforeLastColumn . "{$startRow}");
        $sheet->getStyle("G{$startRow}" . ':' . $this->beforeLastColumn . "{$startRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $nextRow = $startRow + 1;
        $sheet->setCellValue('G' . $nextRow, 'GIÁM ĐỐC');
        $sheet->mergeCells("G{$nextRow}" . ':' . $this->beforeLastColumn . "{$nextRow}");
        $sheet->getStyle("G{$nextRow}:G{$nextRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $nextRow)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $nextRow, 'Người lập');
        $sheet->mergeCells("B{$nextRow}" . ':D' . "{$nextRow}");
        $sheet->getStyle("B{$nextRow}:D{$nextRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . $nextRow)->getFont()->setBold(true);

        $personRow = $nextRow + 6;
        $sheet->setCellValue('G' . $personRow, 'TRƯƠNG HOÀNG LONG');
        $sheet->mergeCells("G{$personRow}" . ':' . $this->beforeLastColumn . "{$personRow}");
        $sheet->getStyle("G{$personRow}:G{$personRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $personRow)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $personRow, 'PHẠM THỊ MỸ HẠNH');
        $sheet->mergeCells("B{$personRow}" . ':D' . "{$personRow}");
        $sheet->getStyle("B{$personRow}:D{$personRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . $personRow)->getFont()->setBold(true);
        // Set white border with white background for the rest of the sheet
        $previousRow = $startRow;
        $sheet->getStyle("A{$previousRow}:{$this->lastColumn}1000")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$previousRow}:{$this->lastColumn}1000")->getBorders()->getAllBorders()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$previousRow}:{$this->lastColumn}1000")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle("A{$previousRow}:{$this->lastColumn}1000")->getFill()->getStartColor()->setRGB('FFFFFF');

        $sheet->getStyle("A{$nextRow}:{$this->lastColumn}{$nextRow}")
        ->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN)
        ->getColor()
        ->setRGB('000000');
    }
} 
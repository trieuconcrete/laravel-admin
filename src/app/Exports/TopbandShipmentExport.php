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
            'E' => 'Địa chỉ',
            'F' => 'Điểm đến',
            'G' => 'Địa chỉ',
            'H' => 'Số tấn',
            'I' => 'Loại hàng',
            'J' => 'TTGT',
            'K' => 'Bộ phận',
            'L' => 'Người gửi',
            'M' => 'Đơn giá',
            'N' => 'Phụ thu kết hợp',
            'O' => 'Phí bốc xếp',
            'P' => 'Thành tiền',
            'Q' => 'Ghi chú'
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
        $productNamesString = $this->formatProductNames($shipment, $sheet, $row);
        $destinationsString = $this->formatDestinations($shipment, $sheet, $row);
        $originAddressesString = $this->formatOriginAddresses($shipment, $sheet, $row);
        $destinationAddressesString = $this->formatDestinationAddresses($shipment, $sheet, $row);

        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $shipment['departure_time']); // Ngày
        $sheet->setCellValue('C' . $row, $shipment['plate_number'] ?? ''); // Số xe
        $sheet->setCellValue('D' . $row, $shipment['origin']); // Điểm đi
        $sheet->setCellValue('E' . $row, $originAddressesString); // Địa chỉ điểm đi
        $sheet->setCellValue('F' . $row, $destinationsString); // Điểm đến
        $sheet->setCellValue('G' . $row, $destinationAddressesString); // Địa chỉ điểm đến
        $sheet->setCellValue('H' . $row, $shipment['cargo_weight'] ?? ''); // Số tấn
        $sheet->setCellValue('I' . $row, $productNamesString); // Loại hàng
        $sheet->setCellValue('J' . $row, ''); // TTGT
        $sheet->setCellValue('K' . $row, ''); // Bộ phận
        $sheet->setCellValue('L' . $row, ''); // Người gửi
        $sheet->setCellValue('M' . $row, $shipment['unit_price'] ?? 0); // Đơn giá
        $sheet->setCellValue('N' . $row, $shipment['total_combined_surcharge'] ?? 0); // Phụ thu kết hợp
        $sheet->setCellValue('O' . $row, $shipment['total_combined_cargo_handling'] ?? 0); // Phí bốc xếp
        $sheet->setCellValue('P' . $row, $shipment['total_amount']); // Thành tiền
        $sheet->setCellValue('Q' . $row, $shipment['notes'] ?? ''); // Ghi chú
        
        // Calculate maximum line count to set proper row height
        $maxLines = max(
            substr_count($productNamesString, "\n") + 1,
            substr_count($destinationsString, "\n") + 1,
            substr_count($originAddressesString, "\n") + 1,
            substr_count($destinationAddressesString, "\n") + 1,
            1 // minimum 1 line
        );
        
        if ($maxLines > 1) {
            $calculatedHeight = $maxLines * 15; // 15 points per line
            $sheet->getRowDimension($row)->setRowHeight($calculatedHeight);
        }
    }

    protected function formatProductNames($shipment, $sheet, $row): string
    {
        // Tạo product names với break line
        $productNames = [];
        // Thêm product_name nếu có
        if (!empty($shipment['product_name'])) {
            $productNames[] = '- ' . trim($shipment['product_name']);
        }
        // Thêm product_name2 nếu có
        if (!empty($shipment['product_name2'])) {
            $productNames[] = '- ' . trim($shipment['product_name2']);
        }
        // Thêm product_name3 nếu có (giả sử bạn muốn thêm product_name3 thay vì duplicate product_name2)
        if (!empty($shipment['product_name3'])) {
            $productNames[] = '- ' . trim($shipment['product_name3']);
        }

        // Thiết lập wrap text cho column I (product names)
        $sheet->getStyle('I' . $row)->getAlignment()->setWrapText(true);

        return implode("\n", array_filter($productNames));
    }

    /**
     * Format destinations with line breaks
     * @param array $shipment
     * @param Worksheet $sheet
     * @param int $row
     * @return string
     */
    protected function formatDestinations($shipment, $sheet, $row): string
    {
        $destinations = [];
        
        // Thêm destination nếu có
        if (!empty($shipment['destination'])) {
            $destinations[] = '- ' . trim($shipment['destination']);
        }
        // Thêm destination2 nếu có
        if (!empty($shipment['destination2'])) {
            $destinations[] = '- ' . trim($shipment['destination2']);
        }
        // Thêm destination3 nếu có
        if (!empty($shipment['destination3'])) {
            $destinations[] = '- ' . trim($shipment['destination3']);
        }

        // Thiết lập wrap text cho column F (destinations)
        $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
        
        return implode("\n", array_filter($destinations));
    }

    /**
     * Format origin addresses with line breaks
     * @param array $shipment
     * @param Worksheet $sheet
     * @param int $row
     * @return string
     */
    protected function formatOriginAddresses($shipment, $sheet, $row): string
    {
        $originAddresses = [];
        
        // Thêm address_origin nếu có
        if (!empty($shipment['address_origin'])) {
            $originAddresses[] = '- ' . trim($shipment['address_origin']);
        }
        // Thêm address_origin2 nếu có
        if (!empty($shipment['address_origin2'])) {
            $originAddresses[] = '- ' . trim($shipment['address_origin2']);
        }
        // Thêm address_origin3 nếu có
        if (!empty($shipment['address_origin3'])) {
            $originAddresses[] = '- ' . trim($shipment['address_origin3']);
        }

        // Thiết lập wrap text cho column E (origin addresses)
        $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);
        
        return implode("\n", array_filter($originAddresses));
    }

    /**
     * Format destination addresses with line breaks
     * @param array $shipment
     * @param Worksheet $sheet
     * @param int $row
     * @return string
     */
    protected function formatDestinationAddresses($shipment, $sheet, $row): string
    {
        $destinationAddresses = [];
        
        // Thêm address_destination nếu có
        if (!empty($shipment['address_destination'])) {
            $destinationAddresses[] = '- ' . trim($shipment['address_destination']);
        }
        // Thêm address_destination2 nếu có
        if (!empty($shipment['address_destination2'])) {
            $destinationAddresses[] = '- ' . trim($shipment['address_destination2']);
        }
        // Thêm address_destination3 nếu có
        if (!empty($shipment['address_destination3'])) {
            $destinationAddresses[] = '- ' . trim($shipment['address_destination3']);
        }

        // Thiết lập wrap text cho column G (destination addresses)
        $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
        
        return implode("\n", array_filter($destinationAddresses));
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
        $sheet->getColumnDimension('D')->setWidth(20);  // Điểm đi (origin)
        $sheet->getColumnDimension('E')->setWidth(25);  // Địa chỉ điểm đi (multi-line origin addresses)
        $sheet->getColumnDimension('F')->setWidth(25);  // Điểm đến (multi-line destinations)
        $sheet->getColumnDimension('G')->setWidth(25);  // Địa chỉ điểm đến (multi-line destination addresses)
        $sheet->getColumnDimension('H')->setWidth(12);  // Số tấn
        $sheet->getColumnDimension('I')->setWidth(30);  // Loại hàng (multi-line product names)
        $sheet->getColumnDimension('J')->setWidth(15);  // TTGT
        $sheet->getColumnDimension('K')->setWidth(15);  // Bộ phận
        $sheet->getColumnDimension('L')->setWidth(15);  // Người gửi
        $sheet->getColumnDimension('M')->setWidth(15);  // Đơn giá
        $sheet->getColumnDimension('N')->setWidth(15);  // Phụ thu kết hợp
        $sheet->getColumnDimension('O')->setWidth(15);  // Phí bốc xếp
        $sheet->getColumnDimension('P')->setWidth(15);  // Thành tiền
        $sheet->getColumnDimension('Q')->setWidth(20);  // Ghi chú
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

        $sheet->getStyle('E1:E' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('F1:F' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('G1:G' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('I1:I' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
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
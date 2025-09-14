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

class PerTripShipmentExport extends BaseShipmentExport
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
        return 'BẢNG KÊ VẬN CHUYỂN THÁNG ' . date('m/Y', strtotime($this->startDate));
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
            'F' => 'Chuyến',
            'G' => 'Số tấn',
            'H' => 'Đơn giá',
            'I' => 'Phụ phí',
            'J' => 'Thành tiền',
            'K' => 'Ghi chú'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $this->setUpHeaderAndFooter($sheet);
        // Table headers - Row 13
        $headers = $this->getHeaders();
        foreach ($headers as $column => $title) {
            $sheet->setCellValue($column . '13', $title);
        }
        // Style the header row
        $firstColumn = array_key_first($headers);
        $lastColumn = array_key_last($headers);
        $sheet->getStyle($firstColumn . '13:' . $lastColumn . '13')->applyFromArray([
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
        
        // Add data rows starting from row 14
        $row = 14;
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
        $this->addSignatureSection($sheet, $row + 5, $this->companyName);
        
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
        $sheet->setCellValue('F' . $row, $shipment['trip_count'] ?? 1); // Chuyến
        $sheet->setCellValue('G' . $row, $shipment['cargo_weight'] ?? ''); // Số tấn
        $sheet->setCellValue('H' . $row, $shipment['unit_price'] ?? 0); // Đơn giá
        $sheet->setCellValue('I' . $row, $shipment['total_expense_deductions']); // Phụ phí
        $sheet->setCellValue('J' . $row, $shipment['total_amount']); // Thành tiền
        $sheet->setCellValue('K' . $row, $shipment['notes'] ?? ''); // Ghi chú
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
        $dataRange = 'A14:' . $this->lastColumn . $summaryRow;
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
        $sheet->getColumnDimension('C')->setWidth(15);  // Số xe
        $sheet->getColumnDimension('D')->setWidth(20);  // Điểm đi
        $sheet->getColumnDimension('E')->setWidth(20);  // Điểm đến
        $sheet->getColumnDimension('F')->setWidth(12);  // Chuyến
        $sheet->getColumnDimension('G')->setWidth(12);  // Số tấn
        $sheet->getColumnDimension('H')->setWidth(12);  // Đơn giá
        $sheet->getColumnDimension('I')->setWidth(12);  // Phụ phí
        $sheet->getColumnDimension('J')->setWidth(15);  // Thành tiền
        $sheet->getColumnDimension('K')->setWidth(20);  // Ghi chú
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        $sheet->getStyle('H14' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I14' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('J14' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        // Set text alignment
        $sheet->getStyle('A14:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F14:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G14:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
} 
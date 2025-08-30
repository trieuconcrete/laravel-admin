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

class CraneShipmentExport extends BaseShipmentExport
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
            'D' => 'Nội dung công việc',
            'E' => 'Loại hàng',
            'F' => 'TTGT',
            'G' => 'Số Lượng',
            'H' => 'Đơn giá',
            'I' => 'Thành tiền',
            'J' => 'Ghi chú'
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
            $row = $this->addDataRow($sheet, $row, $index, $shipment);
            $totalAmount += $shipment['total_amount'];
            $row ++;
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
        if ($shipment['goods']) {
            foreach ($shipment['goods'] as $key => $goods) {
                $index += $key;
                $sheet->setCellValue('A' . $row, $row - 13);
                $sheet->setCellValue('B' . $row, $shipment['departure_time']); // Ngày
                $sheet->setCellValue('C' . $row, $shipment['vehicle_plate_number']); // Số xe
                $sheet->setCellValue('D' . $row, $goods['notes']); // Nội dung công việc
                $sheet->setCellValue('E' . $row, $goods['name']); // Loại hàng
                $sheet->setCellValue('F' . $row, ''); // TTGT
                $sheet->setCellValue('G' . $row, $goods['weight']); // Số Lượng
                $sheet->setCellValue('H' . $row, $goods['unit'] ?? 0); // Đơn giá
                $sheet->setCellValue('I' . $row, ($goods['amount'] ?? 0)); // Thành tiền
                $sheet->setCellValue('J' . $row, ''); // Ghi chú
                if ($key < count($shipment['goods']) - 1) {
                    $row++;
                }
            }
        }
        return $row;
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
        $sheet->getColumnDimension('C')->setWidth(12);  // Số xe
        $sheet->getColumnDimension('D')->setWidth(20);  // Nội dung công việc
        $sheet->getColumnDimension('E')->setWidth(15);  // Loại hàng
        $sheet->getColumnDimension('F')->setWidth(12);  // TTGT
        $sheet->getColumnDimension('G')->setWidth(15);  // Số Lượng
        $sheet->getColumnDimension('H')->setWidth(20);  // Đơn giá
        $sheet->getColumnDimension('I')->setWidth(15);  // Thành tiền
        $sheet->getColumnDimension('J')->setWidth(20);  // Ghi chú
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        $sheet->getStyle('I14:I' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H14' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A14:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C14:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F14:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G14:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
} 
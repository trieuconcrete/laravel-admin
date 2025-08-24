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
        return 'Bảng kê chuyến xe - Xe nâng';
    }

    /**
     * @return string
     */
    protected function getReportTitle(): string
    {
        return 'BẢNG KÊ CHUYẾN XE - XE NÂNG';
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        // return [
        //     'STT',
        //     'Mã chuyến xe',
        //     'Ngày',
        //     'Điểm đi',
        //     'Điểm đến',
        //     'Số chuyến',
        //     'Đơn giá cẩu',
        //     'Thành tiền'
        // ];
        return [
            'A' => 'STT',
            'B' => 'Ngày',
            'C' => 'Số xe',
            'D' => 'Điểm đi',
            'E' => 'Điểm đến',
            'F' => 'Số Lượng',
            'G' => 'Kết hợp',
            'H' => 'Phụ phí chuyến xe',
            'I' => 'Đơn giá',
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
        $sheet->setCellValue('B' . $row, $shipment['departure_time']);
        $sheet->setCellValue('C' . $row, $shipment['vehicle_plate_number']);
        $sheet->setCellValue('D' . $row, $shipment['origin']);
        $sheet->setCellValue('E' . $row, $shipment['destination']);
        $sheet->setCellValue('F' . $row, $shipment['trip_count'] ?? 1);
        $sheet->setCellValue('G' . $row, $shipment['total_combined_surcharge']);
        $sheet->setCellValue('H' . $row, $shipment['total_expense_deductions']);
        $sheet->setCellValue('I' . $row, $shipment['unit_price'] ?? 0);
        $sheet->setCellValue('J' . $row, $shipment['total_amount']);
        $sheet->setCellValue('K' . $row, $shipment['notes'] ?? '');
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
        $sheet->setCellValue('J' . $summaryRow, $totalAmount);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':K' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('J' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // VAT
        $summaryRow++;
        $vatAmount = $totalAmount * 0.08; // Assuming 8% VAT
        $sheet->setCellValue('A' . $summaryRow, 'THUẾ GTGT 8%');
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('J' . $summaryRow, $vatAmount);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':K' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('J' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Amount + VAT
        $summaryRow++;
        $totalAmountVAT = $totalAmount + $vatAmount;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG THANH TOÁN');
        $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
        $sheet->setCellValue('J' . $summaryRow, $totalAmountVAT);
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':K' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('J' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Style the data rows including summary row
        $dataRange = 'A14:K' . $summaryRow;
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
        $sheet->getColumnDimension('B')->setWidth(15);  // Mã chuyến xe
        $sheet->getColumnDimension('C')->setWidth(12);  // Ngày
        $sheet->getColumnDimension('D')->setWidth(15);  // Điểm đi
        $sheet->getColumnDimension('E')->setWidth(15);  // Điểm đến
        $sheet->getColumnDimension('F')->setWidth(12);  // Số chuyến/KM
        $sheet->getColumnDimension('G')->setWidth(15);  // Phụ thu kết hợp
        $sheet->getColumnDimension('H')->setWidth(20);  // Chi phí chuyến xe
        $sheet->getColumnDimension('I')->setWidth(15);  // Đơn giá
        $sheet->getColumnDimension('J')->setWidth(15);  // Thành tiền
        $sheet->getColumnDimension('K')->setWidth(20);  // Ghi chú
    }
} 
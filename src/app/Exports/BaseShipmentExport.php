<?php

namespace App\Exports;

use App\Models\Customer;
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
use Illuminate\Support\Collection;

abstract class BaseShipmentExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $customer;
    protected $shipments;
    protected $startDate;
    protected $endDate;
    protected $shipmentType;

    /**
     * @param Customer $customer
     * @param Collection $shipments
     * @param string $startDate
     * @param string $endDate
     * @param int $shipmentType
     */
    public function __construct(Customer $customer, Collection $shipments, string $startDate, string $endDate, int $shipmentType)
    {
        $this->customer = $customer;
        $this->shipments = $shipments;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->shipmentType = $shipmentType;
    }

    /**
     * @return string
     */
    abstract public function title(): string;

    /**
     * @return string
     */
    abstract protected function getReportTitle(): string;

    /**
     * @return array
     */
    abstract protected function getHeaders(): array;

    /**
     * @param Worksheet $sheet
     */
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
        $sheet->setCellValue('A5', $this->getReportTitle());
        $sheet->setCellValue('A6', '(Từ ngày: ' . date('d/m/Y', strtotime($this->startDate)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($this->endDate)) . ')');
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
        
        $sheet->getStyle('A8')->getFont()->setBold(true);
        
        // Table headers - Row 13
        $headers = $this->getHeaders();
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        
        foreach ($columns as $index => $column) {
            if (isset($headers[$index])) {
                $sheet->setCellValue($column . '13', $headers[$index]);
            }
        }
        
        // Style the header row
        $sheet->getStyle('A13:H13')->applyFromArray([
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
        
        // Add summary row
        $summaryRow = $row;
        $sheet->setCellValue('A' . $summaryRow, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $summaryRow . ':G' . $summaryRow);
        $sheet->setCellValue('H' . $summaryRow, $totalAmount);
        
        // Style the data rows including summary row
        $dataRange = 'A14:H' . $summaryRow;
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
        
        // Style summary row
        $sheet->getStyle('A' . $summaryRow . ':H' . $summaryRow)->getFont()->setBold(true);
        $sheet->getStyle('H' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H' . $summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Add signature section
        $this->addSignatureSection($sheet, $summaryRow + 2, $companyName);
        
        return $sheet;
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
        $sheet->getColumnDimension('G')->setWidth(15);  // Đơn giá
        $sheet->getColumnDimension('H')->setWidth(15);  // Thành tiền
    }

    /**
     * Add data row
     */
    protected function addDataRow(Worksheet $sheet, int $row, int $index, array $shipment)
    {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $shipment['shipment_code']);
        $sheet->setCellValue('C' . $row, $shipment['departure_time']);
        $sheet->setCellValue('D' . $row, $shipment['origin']);
        $sheet->setCellValue('E' . $row, $shipment['destination']);
        
        // Set value based on shipment type
        if ($this->shipmentType == 4) {
            $sheet->setCellValue('F' . $row, $shipment['distance'] ?? 0);
        } else {
            $sheet->setCellValue('F' . $row, $shipment['trip_count'] ?? 1);
        }
        
        // Set unit price based on shipment type
        if ($this->shipmentType == 3) {
            $sheet->setCellValue('G' . $row, $shipment['crane_price'] ?? 0);
        } else {
            $sheet->setCellValue('G' . $row, $shipment['unit_price'] ?? 0);
        }
        
        $sheet->setCellValue('H' . $row, $shipment['total_amount']);
    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        $sheet->getStyle('F14:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G14:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
        // Set text alignment
        $sheet->getStyle('A14:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C14:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F14:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G14:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
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
        
        // Put customer name and company name
        $sheet->setCellValue('B' . ($startRow + 2), $this->customer->name);
        $sheet->setCellValue('G' . ($startRow + 2), $companyName);
        
        $sheet->getStyle('G' . $startRow)->getFont()->setBold(true)->setItalic(true);
        $sheet->getStyle('B' . ($startRow + 2))->getFont()->setBold(true);
        $sheet->getStyle('G' . ($startRow + 2))->getFont()->setBold(true);
        
        $sheet->getStyle('G' . $startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . ($startRow + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
} 
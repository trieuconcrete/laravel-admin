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

    protected $companyName;
    protected $companyAddress;
    protected $companyTaxCode;
    protected $companyPhone;
    protected $companyEmail;

    protected $headers;
    protected $firstColumn;
    protected $lastColumn;
    protected $beforeLastColumn;

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

        // Company information - Header and Footer
        $this->companyName = Setting::get('company_name', 'CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG');
        $this->companyAddress = Setting::get('company_address', 'Số 216, tổ 4, ấp 7, Bình Sơn, Long Thành, Đồng Nai');
        $this->companyTaxCode = Setting::get('company_tax_code', '');
        $this->companyPhone = Setting::get('company_phone', '');
        $this->companyEmail = Setting::get('company_email', '');

        $this->headers = $this->getHeaders();
        $this->firstColumn = array_key_first($this->headers);
        $this->lastColumn = array_key_last($this->headers);
        $this->beforeLastColumn = array_keys($this->headers)[count($this->headers) - 2];
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
     * Summary of setUpHeaderAndFooter
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    protected function setUpHeaderAndFooter(Worksheet $sheet): void
    {
        // Set company information
        $sheet->setCellValue('A1', $this->companyName);
        $sheet->setCellValue('A2', 'Địa chỉ: ' . $this->companyAddress);
        $sheet->setCellValue('A3', 'MST: ' . $this->companyTaxCode);
        $sheet->setCellValue('C3', "Số ĐT: {$this->companyPhone}");
        $sheet->setCellValue('A4', "Email: {$this->companyEmail}");
        $sheet->mergeCells('A1' . ':' . $this->lastColumn . '1');
        $sheet->mergeCells('A2' . ':' . $this->lastColumn . '2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('A4:C4');
        
        // Format company header
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(11);
        
        // Add report title
        $sheet->setCellValue('A5', $this->getReportTitle());
        $sheet->getRowDimension(5)->setRowHeight(25); // Height in points (you can adjust this value)
        $sheet->getRowDimension(6)->setRowHeight(20); // Height in points (you can adjust this value)

        $sheet->setCellValue('A6', '(Từ ngày: ' . date('d/m/Y', strtotime($this->startDate)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($this->endDate)) . ')');
        $sheet->mergeCells('A5' . ':' . $this->lastColumn . '5');
        $sheet->mergeCells('A6' . ':' . $this->lastColumn . '6');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A5:A6')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
        
        // Customer information
        $sheet->setCellValue('A8', $this->customer->name);
        $sheet->setCellValue('A9', 'MST: ' . $this->customer->tax_code);
        $sheet->setCellValue('A10', 'Địa chỉ: ' . $this->customer->address);
        $sheet->setCellValue('A11', 'Email: ' . $this->customer->email);
        $sheet->mergeCells('A8:D8');
        $sheet->mergeCells('A10:G10');
        $sheet->mergeCells('A9:D9');
        $sheet->mergeCells('A11:D11');
        
        $sheet->getStyle('A8')->getFont()->setBold(true);

        // TAX
        $sheet->setCellValue('I10', 'Hóa đơn: 01GTKT0/001');
        $sheet->setCellValue('I11', 'Bảng kê số: 0123456789');
        $sheet->setCellValue('I12', 'ĐVT: VNĐ');

    }

    /**
     * Set number formats
     */
    protected function setNumberFormats(Worksheet $sheet, int $row)
    {
        $sheet->getStyle('F14:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G14' . ':' . $this->lastColumn . '' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        
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
        $sheet->setCellValue('G' . ($startRow + 2), $this->companyName);
        
        $sheet->getStyle('G' . $startRow)->getFont()->setBold(true)->setItalic(true);
        $sheet->getStyle('B' . ($startRow + 2))->getFont()->setBold(true);
        $sheet->getStyle('G' . ($startRow + 2))->getFont()->setBold(true);
    }
} 
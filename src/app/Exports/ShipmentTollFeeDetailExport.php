<?php

namespace App\Exports;

use App\Models\CarRental;
use App\Facades\Setting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ShipmentTollFeeDetailExport implements WithTitle, WithStyles, ShouldAutoSize
{
    protected $carRental;
    protected $shipments;
    protected $tollFeesByDate;
    protected $month;

    /**
     * @param CarRental $carRental
     * @param Collection $shipments
     * @param Collection $tollFeesByDate
     * @param string $month
     */
    public function __construct(CarRental $carRental, $shipments, $tollFeesByDate, string $month = null)
    {
        $this->carRental = $carRental;
        $this->shipments = $shipments;
        $this->tollFeesByDate = $tollFeesByDate;
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
        $vietnameseMonth = $monthDate->format('m/Y');

        // Get company settings
        $companyName = Setting::get('company_name', 'CÔNG TY CỔ PHẦN VẬN TẢI HPL');
        $companyAddress = Setting::get('company_address', '');

        // Header content
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $companyName);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($companyAddress) {
            $sheet->mergeCells('A2:G2');
            $sheet->setCellValue('A2', $companyAddress);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->mergeCells('A4:G4');
        $sheet->setCellValue('A4', "BẢNG KÊ HÓA ĐƠN ĐIỆN TỬ PHÍ CẦU ĐƯỜNG THÁNG {$vietnameseMonth}");
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Customer info
        $sheet->setCellValue('A6', 'Tên khách hàng: ' . $this->carRental->customer->name);
        $sheet->mergeCells('A6:D6');
        $sheet->getStyle('A6')->getFont()->setBold(true);

        // Table headers
        $headers = [
            'A8' => 'STT',
            'B8' => 'Ngày giao dịch',
            'C8' => 'Tên trạm thu phí',
            'D8' => 'Mã giao dịch',
            'E8' => 'Số tiền (VNĐ)',
            'F8' => 'Biển số xe',
            'G8' => 'Ghi chú'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // STT
        $sheet->getColumnDimension('B')->setWidth(15);  // Ngày giao dịch
        $sheet->getColumnDimension('C')->setWidth(25);  // Tên trạm
        $sheet->getColumnDimension('D')->setWidth(20);  // Mã giao dịch
        $sheet->getColumnDimension('E')->setWidth(15);  // Số tiền
        $sheet->getColumnDimension('F')->setWidth(12);  // Biển số xe
        $sheet->getColumnDimension('G')->setWidth(25);  // Ghi chú

        // Add data rows
        $row = 9;
        $totalAmount = 0;
        $stt = 1;

        foreach ($this->tollFeesByDate as $date => $tollFees) {
            foreach ($tollFees as $tollFee) {
                // Get vehicle plate number from shipment on this date
                $shipmentOnDate = $this->shipments->firstWhere('run_date', $date);
                $plateNumber = $shipmentOnDate && $shipmentOnDate->vehicle ? 
                              $shipmentOnDate->vehicle->plate_number : 'N/A';

                $sheet->setCellValue('A' . $row, $stt);
                $sheet->setCellValue('B' . $row, Carbon::parse($date)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $tollFee->station_name);
                $sheet->setCellValue('D' . $row, $tollFee->transaction_code ?? '');
                $sheet->setCellValue('E' . $row, number_format($tollFee->fee_amount, 0, ',', '.'));
                $sheet->setCellValue('F' . $row, $plateNumber);
                $sheet->setCellValue('G' . $row, $tollFee->notes ?? '');

                $totalAmount += $tollFee->fee_amount;
                $stt++;
                $row++;
            }
        }

        // Add totals row if there are toll fees
        if ($stt > 1) {
            $sheet->setCellValue('A' . $row, 'TỔNG CỘNG');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('E' . $row, number_format($totalAmount, 0, ',', '.'));
            $sheet->getStyle('E' . $row)->getFont()->setBold(true);
            
            // Style totals row
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFCCCCCC');

            // Apply borders to data table
            $tableRange = 'A8:G' . $row;
            $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        } else {
            // No toll fees message
            $sheet->setCellValue('A9', 'Không có hóa đơn phí cầu đường trong tháng này');
            $sheet->mergeCells('A9:G9');
            $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A9')->getFont()->setItalic(true);
        }

        // Center align numeric and date columns
        if ($stt > 1) {
            $sheet->getStyle('A9:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B9:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E9:E' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('F9:F' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return $sheet;
    }
} 
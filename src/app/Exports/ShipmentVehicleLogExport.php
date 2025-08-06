<?php

namespace App\Exports;

use App\Models\CarRental;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShipmentVehicleLogExport implements WithMultipleSheets
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
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Nhật ký lộ trình xe' => new ShipmentLogExport($this->carRental, $this->shipments, $this->tollFeesByDate, $this->month),
            'Bảng kê phí cầu đường' => new ShipmentTollFeeExport($this->carRental, $this->shipments, $this->tollFeesByDate, $this->month),
            'Bảng kê hóa đơn điện tử' => new ShipmentTollFeeDetailExport($this->carRental, $this->shipments, $this->tollFeesByDate, $this->month),
        ];
    }
} 
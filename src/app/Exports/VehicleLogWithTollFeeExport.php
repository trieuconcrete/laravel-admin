<?php

namespace App\Exports;

use App\Models\CarRental;
use App\Models\CarRentalVehicleLog;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class VehicleLogWithTollFeeExport implements WithMultipleSheets
{
    protected $carRental;
    protected $vehicleLogs;
    protected $month;

    /**
     * @param CarRental $carRental
     * @param Collection $vehicleLogs
     * @param string $month
     */
    public function __construct(CarRental $carRental, $vehicleLogs, string $month = null)
    {
        $this->carRental = $carRental;
        $this->vehicleLogs = $vehicleLogs;
        $this->month = $month ?? now()->format('m/Y');
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Nhật ký lộ trình xe' => new VehicleLogExport($this->carRental, $this->vehicleLogs, $this->month),
            'Bảng kê phí cầu đường' => new TollFeeExport($this->carRental, $this->vehicleLogs, $this->month),
            'Bảng kê hóa đơn điện tử' => new TollFeeDetailExport($this->carRental, $this->vehicleLogs, $this->month),
        ];
    }
} 
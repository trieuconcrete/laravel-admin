<?php

namespace App\Exports;

class MonthlyRentalShipmentExport extends BaseShipmentExport
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bảng kê chuyến xe - Khách thuê xe tháng';
    }

    /**
     * @return string
     */
    protected function getReportTitle(): string
    {
        return 'BẢNG KÊ CHUYẾN XE - KHÁCH THUÊ XE THÁNG';
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'STT',
            'Mã chuyến xe',
            'Ngày',
            'Điểm đi',
            'Điểm đến',
            'Số chuyến',
            'Đơn giá',
            'Thành tiền'
        ];
    }
} 
<?php

namespace App\Exports;

class PerTripShipmentExport extends BaseShipmentExport
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bảng kê chuyến xe - Khách chạy theo chuyến';
    }

    /**
     * @return string
     */
    protected function getReportTitle(): string
    {
        return 'BẢNG KÊ CHUYẾN XE - KHÁCH CHẠY THEO CHUYẾN';
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'STT',
            'Ngày',
            'Số xe',
            'Điểm đi',
            'Điểm đến',
            'Số chuyến',
            'Kết hợp',
            'Phụ phí chuyến xe',
            'Đơn giá',
            'Thành tiền',
            'Ghi chú'
        ];
    }
} 
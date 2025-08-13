<?php

namespace App\Exports;

class LongDistanceShipmentExport extends BaseShipmentExport
{
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bảng kê chuyến xe - Xe đường dài bắc-nam';
    }

    /**
     * @return string
     */
    protected function getReportTitle(): string
    {
        return 'BẢNG KÊ CHUYẾN XE - XE ĐƯỜNG DÀI BẮC-NAM';
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
            'Số km',
            'Đơn giá/km',
            'Thành tiền'
        ];
    }
} 
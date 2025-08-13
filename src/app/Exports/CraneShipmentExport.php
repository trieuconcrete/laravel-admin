<?php

namespace App\Exports;

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
        return [
            'STT',
            'Mã chuyến xe',
            'Ngày',
            'Điểm đi',
            'Điểm đến',
            'Số chuyến',
            'Đơn giá cẩu',
            'Thành tiền'
        ];
    }
} 
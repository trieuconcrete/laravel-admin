<?php

namespace App\Services;

use App\Models\ShipmentTemplate;
use Illuminate\Support\Facades\Auth;

class ShipmentTemplateService
{
    /**
     * Lưu template shipment
     *
     * @param string $templateName
     * @param array $data
     * @return ShipmentTemplate
     */
    public function store(string $templateName, array $data): ShipmentTemplate
    {
        return Auth::user()->shipmentTemplates()->create([
            'template_name' => $templateName,
            'data' => $data
        ]);
    }

    /**
     * Cập nhật template shipment
     *
     * @param ShipmentTemplate $template
     * @param array $data
     * @return ShipmentTemplate
     */
    public function update(ShipmentTemplate $template, array $data): ShipmentTemplate
    {
        $template->update([
            'data' => $data
        ]);

        return $template;
    }

    /**
     * Xoá template shipment
     *
     * @param ShipmentTemplate $template
     * @return bool|null
     */
    public function delete(ShipmentTemplate $template): ?bool
    {
        return $template->delete();
    }

    /**
     * Lấy tất cả template của user hiện tại
     */
    public function all()
    {
        return Auth::user()->shipmentTemplates()->get();
    }

    /**
     * Lấy template theo ID (chỉ của user hiện tại)
     */
    public function find(int $id): ?ShipmentTemplate
    {
        return Auth::user()->shipmentTemplates()->find($id);
    }
}

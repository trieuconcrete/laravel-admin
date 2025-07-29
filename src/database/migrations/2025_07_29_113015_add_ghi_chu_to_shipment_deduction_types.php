<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ShipmentDeductionType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm record "Ghi chú" vào bảng shipment_deduction_types
        ShipmentDeductionType::create([
            'name' => "Ghi chú",
            'type' => ShipmentDeductionType::TYPE_EXPENSE,
            'status' => 'active',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa record "Ghi chú" khi rollback
        ShipmentDeductionType::where('name', 'Ghi chú')
            ->where('type', ShipmentDeductionType::TYPE_EXPENSE)
            ->delete();
    }
}; 
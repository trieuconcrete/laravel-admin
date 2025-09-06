<?php

use App\Models\ShipmentGood;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipment_goods', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->nullable()->default(0)->after('weight')->comment('Thành tiền (VNĐ)');
        });

        $goods = ShipmentGood::all();
        foreach ($goods as $good) {
            $quantity = $good->quantity ?? 1;
            $amount = $good->unit * $good->weight * $quantity;
            
            $good->update(['amount' => $amount]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_goods', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};

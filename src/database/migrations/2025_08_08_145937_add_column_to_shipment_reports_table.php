<?php

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
        Schema::table('shipment_reports', function (Blueprint $table) {
            $table->unsignedTinyInteger('shipment_type')
                  ->nullable()
                  ->default(1)
                  ->after('monthly')
                  ->comment('1: Khách chạy theo chuyến, 2: Khách thuê xe tháng, 3: Xe nâng, 4: Xe đường dài bắc-nam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            $table->dropColumn('shipment_type');
        });
    }
};

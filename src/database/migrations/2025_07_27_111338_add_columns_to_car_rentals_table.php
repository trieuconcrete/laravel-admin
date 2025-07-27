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
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->integer('overtime_fee_per_hour')->default(0)->after('monthly_rental_fee');
            $table->integer('max_distance')->default(0)->after('overtime_fee_per_hour');
            $table->integer('over_distance_fee_per_km')->default(0)->after('max_distance');
            $table->string('invoice_number', 50)->nullable()->after('over_distance_fee_per_km')->comment('Số hóa đơn');
            $table->string('statement_number', 50)->nullable()->after('invoice_number')->comment('Số bảng kê');
            $table->string('currency', 10)->default('VNĐ')->after('statement_number')->comment('Đơn vị tiền tệ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->dropColumn(['overtime_fee_per_hour', 'max_distance', 'over_distance_fee_per_km', 'invoice_number', 'statement_number', 'currency']);
        });
    }
};

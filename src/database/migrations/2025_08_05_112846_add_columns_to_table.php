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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('is_rental_car')
                  ->nullable()
                  ->default(false)
                  ->after('status')
                  ->comment('Đánh dấu xe thuê từ nhà cung cấp');
            $table->unsignedBigInteger('rental_provider_id')
                  ->nullable()
                  ->after('is_rental_car')
                  ->comment('ID nhà cung cấp cho thuê xe');
        });
        Schema::table('salary_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('salary_type')
                  ->nullable()
                  ->default(1)
                  ->after('employee_id')
                  ->comment('1: Tài xế ăn lương cơ bản, 2: Tài xế ăn lương doanh số');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->boolean('is_rental_car')
                  ->nullable()
                  ->default(false)
                  ->after('status')
                  ->comment('Đánh dấu chuyến hàng sử dụng xe thuê');
            $table->unsignedTinyInteger('shipment_type')
                  ->nullable()
                  ->default(1)
                  ->after('is_rental_car')
                  ->comment('1: Khách chạy theo chuyến, 2: Khách thuê xe tháng, 3: Xe nâng, 4: Xe đường dài bắc-nam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['is_rental_car', 'rental_provider_id']);
        });
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['is_rental_car', 'shipment_type']);
        });
    }
};

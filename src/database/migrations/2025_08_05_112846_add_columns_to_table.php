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
            $table->boolean('is_car_rental')
                  ->nullable()
                  ->default(false)
                  ->after('status')
                  ->comment('Đánh dấu xe thuê từ nhà cung cấp');
            $table->unsignedBigInteger('customer_id')
                  ->nullable()
                  ->after('is_car_rental')
                  ->comment('ID Customer cho thuê xe');
            $table->softDeletes();
        });
        Schema::table('salary_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('salary_type')
                  ->nullable()
                  ->default(1)
                  ->after('employee_id')
                  ->comment('1: Tài xế ăn lương cơ bản, 2: Tài xế ăn lương doanh số');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->boolean('is_car_rental')
                  ->nullable()
                  ->default(false)
                  ->after('status')
                  ->comment('Đánh dấu chuyến hàng sử dụng xe thuê');
            $table->unsignedTinyInteger('shipment_type')
                  ->nullable()
                  ->default(1)
                  ->after('is_car_rental')
                  ->comment('1: Khách chạy theo chuyến, 2: Khách thuê xe tháng, 3: Xe nâng, 4: Xe đường dài bắc-nam');
        });
        DB::statement("ALTER TABLE customers MODIFY COLUMN type ENUM('individual', 'business', 'carrental') NOT NULL DEFAULT 'individual' COMMENT 'Loại khách hàng: cá nhân/doanh nghiệp/cho thuê xe'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['is_car_rental', 'customer_id']);
        });
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['is_car_rental', 'shipment_type']);
        });
    }
};

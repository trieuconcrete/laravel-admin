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
        Schema::table('car_rental_vehicle_logs', function (Blueprint $table) {
            // Đổi start_time, end_time sang kiểu time
            $table->time('start_time')->change();
            $table->time('end_time')->change();
            // Thêm ngày chạy (nullable để tránh lỗi với dữ liệu cũ)
            $table->date('run_date')->nullable()->after('id');
            // Thêm vị trí đi và vị trí đến
            $table->string('start_location')->nullable()->after('end_time');
            $table->string('end_location')->nullable()->after('start_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rental_vehicle_logs', function (Blueprint $table) {
            // Rollback các thay đổi
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->change();
            $table->dropColumn('run_date');
            $table->dropColumn('start_location');
            $table->dropColumn('end_location');
        });
    }
};

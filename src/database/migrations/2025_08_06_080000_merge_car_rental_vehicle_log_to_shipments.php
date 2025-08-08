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
        Schema::table('shipments', function (Blueprint $table) {
            // Thêm reference đến car_rental
            $table->unsignedBigInteger('car_rental_id')
                  ->nullable()
                  ->after('shipment_type')
                  ->comment('ID hợp đồng thuê xe');
            
            // Thông tin thời gian từ vehicle log
            $table->time('start_time')
                  ->nullable()
                  ->after('car_rental_id')
                  ->comment('Thời gian bắt đầu chuyến');
            
            $table->time('end_time')
                  ->nullable()
                  ->after('start_time')
                  ->comment('Thời gian kết thúc chuyến');
            
            $table->date('run_date')
                  ->nullable()
                  ->after('end_time')
                  ->comment('Ngày chạy chuyến');
            
            $table->decimal('overtime_hours', 8, 2)
                  ->default(0)
                  ->after('run_date')
                  ->comment('Số giờ làm thêm');
            
            // Thông tin quãng đường chi tiết (bổ sung cho distance hiện có)
            $table->decimal('start_odometer', 10, 2)
                  ->nullable()
                  ->after('overtime_hours')
                  ->comment('Số km đồng hồ lúc bắt đầu');
            
            $table->decimal('end_odometer', 10, 2)
                  ->nullable()
                  ->after('start_odometer')
                  ->comment('Số km đồng hồ lúc kết thúc');
            
            // Chi phí phát sinh
            $table->decimal('overtime_rate', 15, 2)
                  ->default(0)
                  ->after('end_odometer')
                  ->comment('Đơn giá làm thêm giờ');
            
            $table->decimal('total_overtime_cost', 15, 2)
                  ->default(0)
                  ->after('overtime_rate')
                  ->comment('Tổng chi phí làm thêm giờ');
            
            $table->decimal('parking_fee', 15, 2)
                  ->default(0)
                  ->after('total_overtime_cost')
                  ->comment('Phí đậu xe');
            
            // Foreign key constraint
            $table->foreign('car_rental_id')
                  ->references('id')
                  ->on('car_rentals')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['car_rental_id']);
            $table->dropColumn([
                'car_rental_id',
                'start_time',
                'end_time', 
                'run_date',
                'overtime_hours',
                'start_odometer',
                'end_odometer',
                'overtime_rate',
                'total_overtime_cost',
                'parking_fee'
            ]);
        });
    }
}; 
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
        Schema::create('car_rental_vehicle_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('car_rental_id')->nullable();
            $table->unsignedBigInteger('shipment_id')->nullable();
            
            // Thời gian
            $table->dateTime('start_time')->comment('Thời gian bắt đầu');
            $table->dateTime('end_time')->comment('Thời gian kết thúc');
            $table->decimal('overtime_hours', 8, 2)->default(0)->comment('Thời gian tăng ca (giờ)');
            
            // Quãng đường
            $table->decimal('start_odometer', 10, 2)->comment('Km bắt đầu');
            $table->decimal('end_odometer', 10, 2)->comment('Km kết thúc');
            $table->decimal('total_distance', 10, 2)->comment('Số km đi trong ngày');
            
            // Chi phí
            $table->decimal('overtime_rate', 15, 2)->default(0)->comment('Đơn giá tăng ca');
            $table->decimal('total_overtime_cost', 15, 2)->default(0)->comment('Tổng chi phí phát sinh tăng ca');
            $table->decimal('toll_fee', 15, 2)->default(0)->comment('Phí cầu đường');
            $table->decimal('parking_fee', 15, 2)->default(0)->comment('Phí đậu xe');
            
            // Thông tin khác
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->string('status')->default('completed');
            
            // Timestamps và foreign keys
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('set null');
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('set null');
            
            // Indexes
            $table->index(['vehicle_id', 'start_time']);
            $table->index('driver_id');
            $table->index('car_rental_id');
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_rental_vehicle_logs');
    }
};
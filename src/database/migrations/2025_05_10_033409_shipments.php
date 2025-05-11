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
        // Tạo bảng shipments mới với các trường theo yêu cầu
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_code')->unique()->comment('Mã chuyến hàng');
            
            // Liên kết với hợp đồng
            $table->unsignedBigInteger('contract_id')->nullable()->comment('ID hợp đồng');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('set null');
            
            // Thông tin điểm đi và đến
            $table->string('origin')->comment('Điểm đi');
            $table->string('destination')->comment('Điểm đến');
            
            // Thông tin thời gian
            $table->dateTime('departure_time')->comment('Thời gian khởi hành');
            $table->dateTime('estimated_arrival_time')->comment('Thời gian đến dự kiến');
            
            // Thông tin hàng hóa
            $table->decimal('cargo_weight', 10, 2)->comment('Tải trọng hàng hóa (kg)');
            $table->text('cargo_description')->nullable()->comment('Mô tả hàng hóa'); // Cột mới: Mô tả hàng hóa
            
            // Liên kết với tài xế và lơ xe
            $table->unsignedBigInteger('driver_id')->nullable()->comment('ID tài xế');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
            
            $table->unsignedBigInteger('co_driver_id')->nullable()->comment('ID lơ xe');
            $table->foreign('co_driver_id')->references('id')->on('users')->onDelete('set null');
            
            // Liên kết với phương tiện
            $table->unsignedBigInteger('vehicle_id')->nullable()->comment('ID phương tiện');
            $table->foreign('vehicle_id')->references('vehicle_id')->on('vehicles')->onDelete('set null');
            
            // Thông tin quãng đường và giá
            $table->decimal('distance', 10, 2)->default(0)->comment('Quãng đường (km)'); // Cột mới: Số km
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Đơn giá');
            $table->decimal('crane_price', 15, 2)->default(0)->comment('Đơn giá cẩu hàng'); // Cột mới: Đơn giá cẩu hàng
            $table->boolean('has_crane_service')->default(false)->comment('Có dịch vụ cẩu hàng'); // Cột mới: Có dịch vụ cẩu hay không
            
            // Thông tin khác
            $table->text('notes')->nullable()->comment('Chú thích');
            
            // Trạng thái chuyến hàng
            $table->enum('status', [
                'pending', 'confirmed', 'in_transit', 'delivered', 
                'cancelled', 'delayed', 'completed'
            ])->default('pending')->comment('Trạng thái chuyến hàng');
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable()->comment('Người tạo');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Người cập nhật');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('shipment_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique()->comment('Mã khách hàng');
            $table->string('name')->comment('Tên khách hàng');
            $table->enum('type', ['individual', 'business'])->comment('Loại khách hàng: cá nhân/doanh nghiệp');
            
            // Thông tin liên hệ
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('province')->nullable()->comment('Tỉnh/Thành phố');
            $table->string('district')->nullable()->comment('Quận/Huyện');
            $table->string('ward')->nullable()->comment('Phường/Xã');
            
            // Thông tin bổ sung
            $table->string('tax_code')->nullable()->comment('Mã số thuế');
            $table->date('establishment_date')->nullable()->comment('Ngày sinh (cá nhân) hoặc ngày thành lập (doanh nghiệp)');
            $table->text('website')->nullable()->comment('Website của doanh nghiệp');
            
            // Người liên hệ chính (đối với doanh nghiệp)
            $table->string('primary_contact_name')->nullable()->comment('Tên người liên hệ chính');
            $table->string('primary_contact_phone')->nullable()->comment('SĐT người liên hệ chính');
            $table->string('primary_contact_email')->nullable()->comment('Email người liên hệ chính');
            $table->string('primary_contact_position')->nullable()->comment('Chức vụ người liên hệ chính');
            
            // Thông tin khác
            $table->text('notes')->nullable()->comment('Ghi chú về khách hàng');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

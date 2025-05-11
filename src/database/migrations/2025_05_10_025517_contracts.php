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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code')->unique()->comment('Mã hợp đồng');
            $table->string('title')->comment('Tiêu đề hợp đồng');
            
            // Thông tin liên kết
            $table->unsignedBigInteger('customer_id')->comment('ID khách hàng');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // Thông tin người liên hệ (có thể khác với người liên hệ chính của khách hàng)
            $table->string('contact_name')->nullable()->comment('Người liên hệ của hợp đồng');
            $table->string('contact_phone')->nullable()->comment('Số điện thoại người liên hệ');
            $table->string('contact_email')->nullable()->comment('Email người liên hệ');
            $table->string('contact_position')->nullable()->comment('Chức vụ người liên hệ');
            
            // Thông tin thời gian
            $table->date('start_date')->comment('Ngày bắt đầu hiệu lực');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc hiệu lực');
            $table->date('signing_date')->comment('Ngày ký hợp đồng');
            
            // Thông tin tài chính
            $table->decimal('total_value', 15, 2)->default(0)->comment('Tổng giá trị hợp đồng');
            $table->string('currency', 3)->default('VND')->comment('Đơn vị tiền tệ');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'other'])->default('bank_transfer')->comment('Phương thức thanh toán');
            $table->text('payment_terms')->nullable()->comment('Điều khoản thanh toán');
            
            // Điều khoản vận chuyển
            $table->text('service_description')->nullable()->comment('Mô tả dịch vụ');
            
            // Trạng thái và quản lý 
            $table->enum('status', ['draft', 'pending', 'active', 'completed', 'terminated', 'cancelled'])
                  ->default('draft')
                  ->comment('Trạng thái hợp đồng');
            
            // Thông tin tệp đính kèm
            $table->string('file_path')->nullable()->comment('Đường dẫn đến file hợp đồng');
            $table->string('attachment_paths')->nullable()->comment('Đường dẫn đến các tệp đính kèm');
            
            // Ghi chú
            $table->text('notes')->nullable()->comment('Ghi chú về hợp đồng');
            $table->text('termination_reason')->nullable()->comment('Lý do chấm dứt hợp đồng (nếu có)');
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable()->comment('Người tạo');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Người cập nhật');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Người phê duyệt');
            $table->timestamp('approved_at')->nullable()->comment('Thời gian phê duyệt');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('contract_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
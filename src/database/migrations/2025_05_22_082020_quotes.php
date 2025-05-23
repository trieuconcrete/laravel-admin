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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            
            // Thông tin vận chuyển
            $table->text('pickup_address');
            $table->text('delivery_address');
            $table->decimal('distance', 8, 2)->nullable(); // km
            $table->decimal('cargo_weight', 8, 2); // tấn
            $table->decimal('cargo_volume', 8, 2)->nullable(); // m3
            $table->string('cargo_type'); // loại hàng hóa
            $table->text('cargo_description')->nullable();
            
            // Thông tin phương tiện
            $table->string('vehicle_type'); // loại xe
            $table->integer('vehicle_quantity')->default(1);
            
            // Thông tin thời gian
            $table->datetime('pickup_datetime');
            $table->datetime('delivery_datetime')->nullable();
            $table->boolean('is_round_trip')->default(false);
            
            // Thông tin giá cả
            $table->decimal('base_price', 12, 2);
            $table->decimal('fuel_surcharge', 12, 2)->default(0);
            $table->decimal('loading_fee', 12, 2)->default(0);
            $table->decimal('insurance_fee', 12, 2)->default(0);
            $table->decimal('additional_fee', 12, 2)->default(0);
            $table->text('additional_fee_description')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->decimal('vat_rate', 5, 2)->default(10.00); // %
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2);
            
            // Trạng thái và thông tin xử lý
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired', 'converted'])->default('draft');
            $table->datetime('valid_until');
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            
            // Thông tin nhân viên
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['customer_phone']);
            $table->index(['valid_until']);
            $table->index(['created_by']);
            $table->index(['assigned_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};

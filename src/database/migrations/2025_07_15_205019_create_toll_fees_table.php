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
        Schema::create('toll_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_log_id')->comment('ID nhật ký xe');
            $table->string('station_name')->comment('Tên trạm thu phí');
            $table->string('transaction_code')->nullable()->comment('Mã giao dịch');
            $table->decimal('fee_amount', 15, 2)->comment('Số tiền phí');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('vehicle_log_id')->references('id')->on('car_rental_vehicle_logs')->onDelete('cascade');
            
            // Indexes
            $table->index('vehicle_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toll_fees');
    }
}; 
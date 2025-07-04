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
        Schema::create('shipment_reports', function (Blueprint $table) {
            $table->id();
            $table->string('monthly'); // Tháng theo định dạng YYYY-MM
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total_amount', 15, 2); // Tổng số tiền
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate reports for same customer in same month
            $table->unique(['customer_id', 'monthly']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_reports');
    }
};

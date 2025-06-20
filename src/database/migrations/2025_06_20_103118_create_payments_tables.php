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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('ID of the customer making the payment');
            $table->decimal('amount', 15, 2)->comment('Payment amount');
            $table->date('payment_date')->comment('Date of payment');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->comment('Current status of the payment');
            $table->text('notes')->nullable()->comment('Optional notes about the payment');
            $table->string('method', 50)->nullable()->comment('Payment method, e.g., cash, bank_transfer, credit_card, etc.');
            $table->string('created_by', 100)->nullable()->comment('User who created the payment record');
            $table->string('updated_by', 100)->nullable()->comment('User who last updated the payment record');
            $table->timestamps(); // includes created_at and updated_at
            $table->softDeletes()->comment('Soft delete timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

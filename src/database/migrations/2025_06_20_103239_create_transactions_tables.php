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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->nullable()->comment('Reference to payments table');
            $table->enum('type', ['income', 'expense'])->comment('income: revenue, expense: cost');
            $table->decimal('amount', 15, 2)->comment('Transaction amount');
            $table->string('category', 100)->comment('Transaction category, e.g., Salary, Food, Shopping...');
            $table->text('description')->nullable()->comment('Detailed description');
            $table->date('transaction_date')->comment('Date of transaction');
            $table->string('created_by', 100)->nullable()->comment('User who created or performed the transaction');
            $table->timestamps(); // includes created_at and updated_at

            $table->foreign('payment_id')->references('id')->on('payments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

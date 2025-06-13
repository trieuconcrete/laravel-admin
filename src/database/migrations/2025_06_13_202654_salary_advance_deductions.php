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
        Schema::create('salary_advance_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_advance_request_id');
            $table->date('deduction_month');
            $table->decimal('deduction_amount', 15, 2);
            $table->enum('deduction_type', ['salary', 'bonus', 'other'])->default('salary');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('salary_advance_request_id')
                  ->references('id')
                  ->on('salary_advance_requests')
                  ->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advance_deductions');
    }
};
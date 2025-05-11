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
        Schema::create('salary_details', function (Blueprint $table) {
            $table->id('salary_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('period_id');
            $table->decimal('base_salary', 15, 2);
            $table->decimal('working_days', 5, 1);
            $table->decimal('fuel_allowance', 15, 2)->default(0);
            $table->decimal('meal_allowance', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('penalty', 15, 2)->default(0);
            $table->decimal('social_insurance', 15, 2)->default(0);
            $table->decimal('health_insurance', 15, 2)->default(0);
            $table->decimal('income_tax', 15, 2)->default(0);
            $table->decimal('other_deduction', 15, 2)->default(0);
            $table->decimal('total_salary', 15, 2)->nullable();
            $table->decimal('net_salary', 15, 2)->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'paid', 'rejected'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['bank_transfer', 'cash', 'other'])->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('period_id')->references('period_id')->on('salary_periods');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('set null');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_details');
    }
};

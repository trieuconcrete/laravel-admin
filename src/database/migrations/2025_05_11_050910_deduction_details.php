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
        Schema::create('deduction_details', function (Blueprint $table) {
            $table->id('deduction_detail_id');
            $table->unsignedBigInteger('salary_id');
            $table->unsignedBigInteger('deduction_type_id');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('salary_id')->references('salary_id')->on('salary_details')->onDelete('cascade');
            $table->foreign('deduction_type_id')->references('deduction_type_id')->on('deduction_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_details');
    }
};

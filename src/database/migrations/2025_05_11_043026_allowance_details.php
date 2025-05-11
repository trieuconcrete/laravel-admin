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
        Schema::create('allowance_details', function (Blueprint $table) {
            $table->id('allowance_detail_id');
            $table->unsignedBigInteger('salary_id');
            $table->unsignedBigInteger('allowance_type_id');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('salary_id')->references('salary_id')->on('salary_details')->onDelete('cascade');
            $table->foreign('allowance_type_id')->references('allowance_type_id')->on('allowance_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_details');
    }
};

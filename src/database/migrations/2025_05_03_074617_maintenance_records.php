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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('maintenance_type', 100);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('service_provider', 100)->nullable();
            $table->string('status', 30)->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('vehicle_id')
                  ->references('vehicle_id')
                  ->on('vehicles')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['vehicle_id', 'status']);
            $table->index('start_date');
            $table->index('maintenance_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};

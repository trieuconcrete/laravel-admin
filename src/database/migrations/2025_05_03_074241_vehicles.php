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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicle_id');
            $table->string('plate_number', 20)->unique();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->float('capacity')->nullable();
            $table->integer('manufactured_year')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();

            // Foreign key
            $table->foreign('vehicle_type_id')
                  ->references('vehicle_type_id')
                  ->on('vehicle_types')
                  ->onDelete('restrict');
            
            // Foreign key
            $table->foreign('driver_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            // Index
            $table->index('status');
            $table->index('plate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

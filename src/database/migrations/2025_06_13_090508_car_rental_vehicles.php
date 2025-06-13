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
        Schema::create('car_rental_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_rental_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('product_name');
            $table->string('unit')->default('ngày'); // tháng, ngày, km, etc.
            $table->integer('amount')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_rental_vehicles');
    }
};

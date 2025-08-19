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
        Schema::table('shipment_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('car_rental_id')->nullable()->after('customer_id');
            $table->index('car_rental_id');
            $table->foreign('car_rental_id')->references('id')->on('car_rentals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            $table->dropForeign(['car_rental_id']);
            $table->dropIndex(['car_rental_id']);
            $table->dropColumn('car_rental_id');
        });
    }
}; 
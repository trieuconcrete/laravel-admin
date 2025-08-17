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
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('currency');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('departure_point')->nullable()->after('end_date');
            $table->string('destination_point')->nullable()->after('departure_point');
            $table->string('product_name')->nullable()->after('destination_point');
            $table->string('contract_number')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'departure_point', 'destination_point', 'product_name', 'contract_number']);
        });
    }
};

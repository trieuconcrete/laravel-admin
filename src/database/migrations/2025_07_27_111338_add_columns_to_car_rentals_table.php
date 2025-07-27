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
            $table->integer('overtime_fee_per_hour')->default(0);
            $table->integer('max_distance')->default(0);
            $table->integer('over_distance_fee_per_km')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->dropColumn('overtime_fee_per_hour');
            $table->dropColumn('max_distance');
            $table->dropColumn('over_distance_fee_per_km');
        });
    }
};

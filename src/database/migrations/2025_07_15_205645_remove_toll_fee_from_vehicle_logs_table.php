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
        Schema::table('car_rental_vehicle_logs', function (Blueprint $table) {
            if (Schema::hasColumn('car_rental_vehicle_logs', 'toll_fee')) {
                $table->dropColumn('toll_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rental_vehicle_logs', function (Blueprint $table) {
            $table->decimal('toll_fee', 15, 2)->default(0)->comment('Phí cầu đường');
        });
    }
}; 
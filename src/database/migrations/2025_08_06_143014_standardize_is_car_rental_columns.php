<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standardize vehicles table
        if (Schema::hasColumn('vehicles', 'is_rental_car')) {
            // Copy data from is_rental_car to is_car_rental if is_car_rental doesn't have data
            DB::statement('UPDATE vehicles SET is_car_rental = is_rental_car WHERE is_car_rental IS NULL OR is_car_rental = 0');
            
            // Drop the old column
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('is_rental_car');
            });
        }

        // Standardize shipments table
        if (Schema::hasColumn('shipments', 'is_rental_car')) {
            // Copy data from is_rental_car to is_car_rental if is_car_rental doesn't have data
            DB::statement('UPDATE shipments SET is_car_rental = is_rental_car WHERE is_car_rental IS NULL OR is_car_rental = 0');
            
            // Drop the old column
            Schema::table('shipments', function (Blueprint $table) {
                $table->dropColumn('is_rental_car');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate is_rental_car columns for rollback
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'is_rental_car')) {
                $table->boolean('is_rental_car')->nullable()->default(false)->after('is_car_rental');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'is_rental_car')) {
                $table->boolean('is_rental_car')->nullable()->default(false)->after('is_car_rental');
            }
        });

        // Copy data back for rollback
        DB::statement('UPDATE vehicles SET is_rental_car = is_car_rental');
        DB::statement('UPDATE shipments SET is_rental_car = is_car_rental');
    }
};

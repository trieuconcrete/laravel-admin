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
        Schema::table('shipments', function (Blueprint $table) {
            // Add trip ton columns
            if (!Schema::hasColumn('shipments', 'trip_ton')) {
                $table->decimal('trip_ton', 10, 2)->nullable()->default(0)->after('cargo_weight')->comment('Số tấn chuyến 1');
            }
            if (!Schema::hasColumn('shipments', 'trip_ton2')) {
                $table->decimal('trip_ton2', 10, 2)->nullable()->default(0)->after('trip_ton')->comment('Số tấn chuyến 2');
            }
            if (!Schema::hasColumn('shipments', 'trip_ton3')) {
                $table->decimal('trip_ton3', 10, 2)->nullable()->default(0)->after('trip_ton2')->comment('Số tấn chuyến 3');
            }

            // Add trip price columns
            if (!Schema::hasColumn('shipments', 'trip_price')) {
                $table->decimal('trip_price', 15, 2)->nullable()->default(0)->after('trip_ton3')->comment('Giá chuyến 1');
            }
            if (!Schema::hasColumn('shipments', 'trip_price2')) {
                $table->decimal('trip_price2', 15, 2)->nullable()->default(0)->after('trip_price')->comment('Giá chuyến 2');
            }
            if (!Schema::hasColumn('shipments', 'trip_price3')) {
                $table->decimal('trip_price3', 15, 2)->nullable()->default(0)->after('trip_price2')->comment('Giá chuyến 3');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Drop new trip_* columns only
            if (Schema::hasColumn('shipments', 'trip_price3')) {
                $table->dropColumn('trip_price3');
            }
            if (Schema::hasColumn('shipments', 'trip_price2')) {
                $table->dropColumn('trip_price2');
            }
            if (Schema::hasColumn('shipments', 'trip_price')) {
                $table->dropColumn('trip_price');
            }

            if (Schema::hasColumn('shipments', 'trip_ton3')) {
                $table->dropColumn('trip_ton3');
            }
            if (Schema::hasColumn('shipments', 'trip_ton2')) {
                $table->dropColumn('trip_ton2');
            }
            if (Schema::hasColumn('shipments', 'trip_ton')) {
                $table->dropColumn('trip_ton');
            }
        });
    }
};

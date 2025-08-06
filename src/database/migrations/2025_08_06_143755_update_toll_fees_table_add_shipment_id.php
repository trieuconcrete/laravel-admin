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
        Schema::table('toll_fees', function (Blueprint $table) {
            // Add shipment_id column
            $table->unsignedBigInteger('shipment_id')->nullable()->after('vehicle_log_id');
            
            // Make vehicle_log_id nullable if it's not already
            $table->unsignedBigInteger('vehicle_log_id')->nullable()->change();
            
            // Add foreign key constraint for shipment_id
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('toll_fees', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['shipment_id']);
            
            // Drop shipment_id column
            $table->dropColumn('shipment_id');
            
            // Revert vehicle_log_id to not nullable if needed
            // Note: This might fail if there are null values, so we'll handle it carefully
            try {
                $table->unsignedBigInteger('vehicle_log_id')->nullable(false)->change();
            } catch (\Exception $e) {
                // If there are null values, we can't make it not nullable
                // This is expected behavior for rollback
            }
        });
    }
};

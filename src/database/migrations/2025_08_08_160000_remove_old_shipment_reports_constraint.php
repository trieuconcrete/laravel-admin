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
            // Try to drop the old unique constraint if it exists
            try {
                $table->dropUnique(['customer_id', 'monthly']);
            } catch (\Exception $e) {
                // Constraint might not exist, continue
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            // Restore the old unique constraint
            $table->unique(['customer_id', 'monthly'], 'shipment_reports_customer_id_monthly_unique');
        });
    }
};
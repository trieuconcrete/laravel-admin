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
            // Add new unique constraint that includes shipment_type
            $table->unique(['customer_id', 'monthly', 'shipment_type'], 'shipment_reports_customer_monthly_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('shipment_reports_customer_monthly_type_unique');
        });
    }
}; 
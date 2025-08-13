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
        Schema::table('shipment_reports', function (Blueprint $table) {
            // First, add the new constraint with a different name
            $table->unique(
                ['customer_id', 'monthly', 'shipment_type', 'statement_start_date', 'statement_end_date'], 
                'shipment_reports_customer_monthly_type_date_unique'
            );
        });
        
        // Then drop the old constraint using raw SQL to avoid foreign key issues
        DB::statement('ALTER TABLE shipment_reports DROP INDEX shipment_reports_customer_monthly_type_unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('shipment_reports_customer_monthly_type_date_unique');
        });
        
        // Restore the old constraint
        DB::statement('ALTER TABLE shipment_reports ADD UNIQUE KEY shipment_reports_customer_monthly_type_unique (customer_id, monthly, shipment_type)');
    }
}; 
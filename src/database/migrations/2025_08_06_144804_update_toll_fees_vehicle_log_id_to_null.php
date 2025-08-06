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
        // Set vehicle_log_id to null for all toll fees
        DB::table('toll_fees')->update(['vehicle_log_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration sets vehicle_log_id to null
        // Rollback would require restoring the original values
        // Since we don't have the original values, we'll leave this empty
        // In a real scenario, you might want to backup the data before running this migration
    }
};

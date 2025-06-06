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
        Schema::table('shipment_deductions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['shipment_deduction_type_id']);
            
            // Modify the column to be nullable
            $table->unsignedBigInteger('shipment_deduction_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('shipment_deduction_type_id')->nullable()->change();
        });
    }
};

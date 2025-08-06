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
        Schema::table('shipment_deduction_types', function (Blueprint $table) {
            $table->integer('order')->default(9999)->after('status')->comment('Order of the deduction type for display purposes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_deduction_types', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};

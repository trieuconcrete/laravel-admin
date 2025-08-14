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
            $table->boolean('is_overtime_at_noon')
                  ->default(false)
                  ->after('overtime_rate')
                  ->comment('Có tăng ca trưa hay không');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('is_overtime_at_noon');
        });
    }
}; 
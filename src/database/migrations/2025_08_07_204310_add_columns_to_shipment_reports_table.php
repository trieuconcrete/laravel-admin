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
            $table->date('statement_start_date')->nullable()
                ->after('monthly')
                ->comment('Ngày bắt đầu bảng kê');
            $table->date('statement_end_date')->nullable()
                ->after('statement_start_date')
                ->comment('Ngày kết thúc bảng kê');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_reports', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn(['statement_start_date', 'statement_end_date']);
        });
    }
};

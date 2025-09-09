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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_insurance')->default(true)->after('salary_by_percent')->comment('Có đóng bảo hiểm xã hội');
            $table->date('insurance_start_date')->nullable()->after('has_insurance')->comment('Ngày bắt đầu đóng bảo hiểm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_insurance', 'insurance_start_date']);
        });
    }
};

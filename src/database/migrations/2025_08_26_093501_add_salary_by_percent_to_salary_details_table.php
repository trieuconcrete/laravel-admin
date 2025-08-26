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
        Schema::table('salary_details', function (Blueprint $table) {
            $table->decimal('salary_by_percent', 5, 2)
                  ->nullable()
                  ->default(12.00)
                  ->after('salary_type')
                  ->comment('Phần trăm lương theo doanh số (%) - snapshot khi tạo kỳ lương');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropColumn('salary_by_percent');
        });
    }
};

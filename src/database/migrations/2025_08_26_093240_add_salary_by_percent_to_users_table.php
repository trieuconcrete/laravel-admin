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
            $table->decimal('salary_by_percent', 5, 2)
                  ->nullable()
                  ->default(12.00)
                  ->after('salary_type')
                  ->comment('Phần trăm lương theo doanh số (%) - chỉ áp dụng cho tài xế ăn lương doanh số');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary_by_percent');
        });
    }
};

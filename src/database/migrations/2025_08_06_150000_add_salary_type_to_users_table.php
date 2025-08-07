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
            $table->unsignedTinyInteger('salary_type')
                  ->nullable()
                  ->default(1)
                  ->after('salary_advance_amount')
                  ->comment('1: Tài xế ăn lương cơ bản, 2: Tài xế ăn lương doanh số');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
    }
}; 
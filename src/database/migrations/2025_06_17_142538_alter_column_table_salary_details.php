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
            $table->dropColumn(['meal_allowance', 'fuel_allowance', 'other_allowance']);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->decimal('meal_allowance', 15, 2)->default(0);
            $table->decimal('fuel_allowance', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->dropColumn(['total_allowance', 'total_expenses']);
        });
    }
};

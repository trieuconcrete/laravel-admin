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
        Schema::table('salary_advance_requests', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('type');
        });

        Schema::table('salary_advance_requests', function (Blueprint $table) {
            // Recreate the enum column with the new value 'payment'
            $table->enum('type', ['salary', 'bonus', 'penalty', 'payment', 'other'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_advance_requests', function (Blueprint $table) {
            // Drop the column
            $table->dropColumn('type');
        });

        Schema::table('salary_advance_requests', function (Blueprint $table) {
            // Recreate the original enum column
            $table->enum('type', ['salary', 'bonus', 'penalty', 'other'])->nullable();
        });
    }
}; 
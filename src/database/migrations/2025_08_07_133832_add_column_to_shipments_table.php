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
            // Company columns
            $table->string('company')->nullable()->after('destination3');
            $table->string('company2')->nullable()->after('company');
            $table->string('company3')->nullable()->after('company2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn([
                'company',
                'company2', 
                'company3',
            ]);
        });
    }
};

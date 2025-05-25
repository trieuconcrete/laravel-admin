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
        Schema::table('driver_licenses', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->change();
            $table->date('issue_date')->nullable()->change();
            $table->string('issued_by')->nullable()->change();
            $table->string('license_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_licenses', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
            $table->date('issue_date')->nullable(false)->change();
            $table->string('issued_by')->nullable(false)->change();
            $table->string('license_number')->nullable(false)->change();
        });
    }
};

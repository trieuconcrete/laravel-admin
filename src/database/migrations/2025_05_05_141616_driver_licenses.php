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
        Schema::create('driver_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('license_number', 50);
            $table->string('license_type', 20); // A1, A2, B1, B2, C, D, E, F
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('issued_by', 100); // Nơi cấp
            $table->string('license_file', 255)->nullable(); // Đường dẫn đến file bằng lái
            $table->string('status', 30)->default('valid'); // valid, expired, expiring_soon, revoked
            $table->timestamps();

            // Indexes
            $table->index(['id', 'license_type']);
            $table->index('license_number');
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

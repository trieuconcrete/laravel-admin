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
        Schema::create('rental_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên khách hàng');
            $table->string('phone', 20)->unique()->comment('Số điện thoại');
            $table->text('address')->nullable()->comment('Địa chỉ');
            $table->string('email')->nullable()->comment('Email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_providers');
    }
};

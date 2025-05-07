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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('employee_code', 20)->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->date('birthday')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 12)->nullable();
            $table->string('id_number', 20)->nullable();
            $table->text('address', 500)->nullable();
            $table->date('join_date')->nullable();
            $table->string('role')->default('user')->comment('admin, user, manager, staff, driver');
            $table->boolean('status')->default(1); // 1: active, 0: inactive
            $table->string('avatar')->nullable();
            $table->string('profile_image')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();

            // index
            $table->index('employee_code');
            $table->index('position_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

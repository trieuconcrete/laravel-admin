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
            // Xóa index cũ
            $table->dropUnique('users_email_unique');
            $table->dropUnique('users_employee_code_unique');
            $table->dropUnique('users_username_unique');

            // Tạo index unique mới có `deleted_at`
            $table->unique(['email', 'deleted_at']);
            $table->unique(['employee_code', 'deleted_at']);
            $table->unique(['username', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa index unique mới
            $table->dropUnique(['email', 'deleted_at']);
            $table->dropUnique(['employee_code', 'deleted_at']);
            $table->dropUnique(['username', 'deleted_at']);

            // Tạo lại index unique
            $table->unique(['email']);
            $table->unique(['employee_code']);
            $table->unique(['username']);
        });
    }
};

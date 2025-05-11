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
        Schema::create('departments', function (Blueprint $table) {
            $table->id('department_id');
            $table->string('department_name', 100);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable()->comment('ID của nhân viên quản lý');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Thêm foreign key nếu bảng employees đã tồn tại
            if (Schema::hasTable('users')) {
                $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

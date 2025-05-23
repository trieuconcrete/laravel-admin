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
        Schema::create('quote_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, sent, approved, rejected, etc.
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // Lưu thông tin thay đổi
            $table->string('performed_by')->nullable();
            $table->timestamps();
            
            $table->index(['quote_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_histories');
    }
};

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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('document_type', 50);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_number', 50)->nullable();
            $table->string('document_file', 255)->nullable();
            $table->string('status', 30)->default('valid');
            $table->timestamps();

            // Foreign key
            $table->foreign('vehicle_id')
                  ->references('vehicle_id')
                  ->on('vehicles')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['vehicle_id', 'document_type']);
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};

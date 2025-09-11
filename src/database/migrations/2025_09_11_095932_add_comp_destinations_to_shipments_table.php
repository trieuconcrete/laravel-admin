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
            $table->string('address_destination')->nullable()->after('company3');
            $table->string('address_destination2')->nullable()->after('address_destination');
            $table->string('address_destination3')->nullable()->after('address_destination2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['address_destination', 'address_destination2', 'address_destination3']);
        });
    }
};
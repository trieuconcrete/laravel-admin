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
            $table->string('destination2')->after('destination')->nullable()->comment('Secondary destination for the shipment');
            $table->string('destination3')->after('destination2')->nullable()->comment('Tertiary destination for the shipment');
            $table->string('origin2')->after('origin')->nullable()->comment('Secondary destination for the shipment');
            $table->string('origin3')->after('origin2')->nullable()->comment('Tertiary destination for the shipment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['destination2', 'destination3']);
            $table->dropColumn(['origin2', 'origin3']);
        });
    }
};

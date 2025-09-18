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
            $table->string('product_name1')->nullable()->after('address_destination3')->comment('Tên hàng hóa 1');
            $table->string('product_name2')->nullable()->after('product_name1')->comment('Tên hàng hóa 2');
            $table->string('product_name3')->nullable()->after('product_name2')->comment('Tên hàng hóa 3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['product_name1', 'product_name2', 'product_name3']);
        });
    }
};

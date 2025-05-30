<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('cargo_weight', 12, 2)->nullable()->change();
            $table->decimal('distance', 12, 2)->nullable()->change();
            $table->decimal('unit_price', 12, 2)->nullable()->change();
            $table->decimal('crane_price', 12, 2)->nullable()->change();
            $table->boolean('has_crane_service')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('cargo_weight', 12, 2)->nullable()->change();
            $table->decimal('distance', 12, 2)->nullable()->change();
            $table->decimal('unit_price', 12, 2)->nullable()->change();
            $table->decimal('crane_price', 12, 2)->nullable()->change();
            $table->boolean('has_crane_service')->nullable()->change();
        });
    }
};

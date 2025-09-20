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
            // Rename company columns to address_origin columns
            $table->renameColumn('company', 'address_origin');
            $table->renameColumn('company2', 'address_origin2');
            $table->renameColumn('company3', 'address_origin3');
            
            // Rename product_name1 to product_name
            $table->renameColumn('product_name1', 'product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('address_origin', 'company');
            $table->renameColumn('address_origin2', 'company2');
            $table->renameColumn('address_origin3', 'company3');
            
            // Reverse product_name rename
            $table->renameColumn('product_name', 'product_name1');
        });
    }
};

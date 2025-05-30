<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('shipment_goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->string('name');
            $table->integer('quantity');
            $table->string('unit')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipment_goods');
    }
};

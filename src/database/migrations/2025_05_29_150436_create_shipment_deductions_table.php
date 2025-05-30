<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('shipment_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->unsignedBigInteger('shipment_deduction_type_id');
            $table->bigInteger('user_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
            $table->foreign('shipment_deduction_type_id')->references('id')->on('shipment_deduction_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipment_deductions');
    }
};

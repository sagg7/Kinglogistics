<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_equipment_type_id');
            $table->unsignedBigInteger('carrier_id');
            $table->string('name');
            $table->enum('status', ['available', 'unavailable']);
            $table->string('description', 512);
            $table->timestamps();

            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_equipment_type_id')->references('id')->on('carrier_equipment_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrier_equipment');
    }
}

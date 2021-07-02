<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rate_group_id');
            $table->unsignedBigInteger('shipper_id');
            $table->unsignedBigInteger('zone_id');
            $table->double('start_mileage');
            $table->double('end_mileage');
            $table->double('shipper_rate');
            $table->double('carrier_rate');
            $table->timestamps();

            $table->foreign('rate_group_id')->references('id')->on('rate_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
}

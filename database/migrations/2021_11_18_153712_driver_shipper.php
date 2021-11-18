<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DriverShipper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_shipper', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->index();
            $table->unsignedBigInteger('shipper_id')->index();
            $table->timestamps();

            $table->primary(['shipper_id', 'driver_id']);
            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

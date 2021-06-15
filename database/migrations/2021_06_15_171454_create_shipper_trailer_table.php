<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipperTrailerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipper_trailer', function (Blueprint $table) {
            $table->unsignedBigInteger('shipper_id')->index();
            $table->unsignedBigInteger('trailer_id')->index();

            $table->primary(['shipper_id', 'trailer_id']);
            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('trailer_id')->references('id')->on('trailers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipper_trailer');
    }
}

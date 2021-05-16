<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('trailer_type_id');
            $table->string('number');
            $table->string('plate')->nullable();
            $table->string('vin')->nullable();
            $table->enum('status', ['rented', 'available', 'oos',]); // oos => out of service
            $table->unsignedTinyInteger('inactive')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trailer_type_id')->references('id')->on('trailer_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trailers');
    }
}

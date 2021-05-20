<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('turn_id')->comment('Hace referencia al array de turnos dentro de los traits de Drivers');
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('trailer_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->unsignedTinyInteger('inactive')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('trailer_id')->references('id')->on('trailers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('truck_id')->references('id')->on('trucks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}

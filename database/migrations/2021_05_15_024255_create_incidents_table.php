<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_type_id');
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('trailer_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('sanction', ['warning', 'fine', 'termination']);
            $table->date('date');
            $table->string('location');
            $table->string('description', 512)->nullable();
            $table->string('excuse', 512)->nullable();
            $table->string('safety_signature', 512)->nullable();
            $table->string('driver_signature', 512)->nullable();
            $table->unsignedTinyInteger('refuse_sign')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('incident_type_id')->references('id')->on('incident_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('truck_id')->references('id')->on('trucks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('trailer_id')->references('id')->on('trailers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidents');
    }
}

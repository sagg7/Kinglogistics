<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turns', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('zone_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('zone_id')->references('id')->on('zones')->onUpdate('cascade')->onDelete('cascade');
            //$table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('turns');
    }
}

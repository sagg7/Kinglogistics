<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoadLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('road_loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_id');
            $table->unsignedInteger('deadhead_miles')->nullable();
            $table->unsignedInteger('trailer_type');
            $table->enum('load_size', ['full', 'partial']);
            $table->unsignedInteger('length')->nullable();
            $table->unsignedDecimal('pay_rate')->nullable();
            $table->timestamps();

            $table->foreign('load_id')->references('id')->on('loads')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('road_loads');
    }
}

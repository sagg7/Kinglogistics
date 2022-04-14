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
            $table->unsignedBigInteger('trailer_type_id');
            $table->unsignedBigInteger('mode_id')->nullable();
            $table->unsignedBigInteger('origin_city_id')->nullable();
            $table->unsignedBigInteger('destination_city_id')->nullable();
            $table->unsignedInteger('deadhead_miles')->nullable();
            $table->enum('load_size', ['full', 'partial']);
            $table->unsignedInteger('length')->nullable();
            $table->unsignedDecimal('pay_rate')->nullable();
            $table->timestamp('origin_late_pick_up_date');
            $table->timestamp('destination_late_pick_up_date');
            $table->timestamp('origin_early_pick_up_date');
            $table->timestamp('destination_early_pick_up_date');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('cube');
            $table->unsignedInteger('pieces');
            $table->unsignedInteger('pallets');
            $table->timestamps();

            $table->foreign('load_id')->references('id')->on('loads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('trailer_type_id')->references('id')->on('load_trailer_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('mode_id')->references('id')->on('load_modes')->onUpdate('cascade')->onDelete('cascade');
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

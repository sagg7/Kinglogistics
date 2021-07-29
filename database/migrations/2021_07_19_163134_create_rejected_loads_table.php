<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectedLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejected_loads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('load_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->timestamps();

            $table->foreign('load_id')
                ->references('id')
                ->on('loads')
                ->onDelete('cascade');

            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejected_loads');
    }
}

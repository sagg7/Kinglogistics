<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_id');
            $table->string('url', 512);
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
        Schema::dropIfExists('load_photos');
    }
}

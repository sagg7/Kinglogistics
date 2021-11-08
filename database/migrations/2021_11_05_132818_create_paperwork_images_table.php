<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperworkImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paperwork_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paperwork_id');
            $table->string('url', 512);
            $table->unsignedInteger('number');
            $table->timestamps();

            $table->foreign('paperwork_id')->references('id')->on('paperwork')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paperwork_images');
    }
}

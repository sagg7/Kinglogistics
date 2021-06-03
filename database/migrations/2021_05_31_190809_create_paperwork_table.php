<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paperwork', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('required')->nullable();
            $table->enum('type', ['carrier', 'driver', 'truck', 'trailer']);
            $table->text('template')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simple_paperwork');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperworkFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paperwork_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paperwork_id');
            $table->unsignedBigInteger('related_id');
            $table->string('url', 512);
            $table->date('expiration_date')->nullable();
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
        Schema::dropIfExists('paperwork_files');
    }
}

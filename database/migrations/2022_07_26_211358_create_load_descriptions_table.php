<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->string('name');
            $table->string('name_spanish');
            $table->string('text', 1024);
            $table->timestamps();

            $table->foreign('broker_id')->references('id')->on('brokers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_descriptions');
    }
}

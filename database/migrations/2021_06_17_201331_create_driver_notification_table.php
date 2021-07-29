<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_safety_message', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->index();
            $table->unsignedBigInteger('safety_message_id')->index();

            $table->primary(['driver_id', 'safety_message_id']);
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('safety_message_id')->references('id')->on('safety_messages')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_safety_message');
    }
}

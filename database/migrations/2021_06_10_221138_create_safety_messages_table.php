<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetyMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safety_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('turn_id')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('message_json')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('safety_messages');
    }
}

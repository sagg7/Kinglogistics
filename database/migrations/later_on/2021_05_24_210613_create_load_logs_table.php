<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_id');
            $table->unsignedBigInteger('user_id')->comment('Referencia al usuario o shipper que creo la orden de loads');
            $table->unsignedInteger('quantity');
            $table->enum('type', ['shipper', 'user']);
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
        Schema::dropIfExists('load_logs');
    }
}

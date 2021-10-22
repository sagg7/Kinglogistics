<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDieselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diesels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('carrier_payment_id')->nullable();
            $table->string("card");
            $table->date("date");
            $table->string("odometer");
            $table->string("location");
            $table->double("fees");
            $table->double("price", 8,3);
            $table->double("quantity");
            $table->double("discount");
            $table->double("amount");
            $table->timestamps();

            $table->foreign('carrier_payment_id')->references('id')->on('carrier_payments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('truck_id')->references('id')->on('trucks')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diesels');
    }
}

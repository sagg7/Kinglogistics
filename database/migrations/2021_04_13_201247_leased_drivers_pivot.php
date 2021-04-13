<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leased_driver', function (Blueprint $table) {
            $table->unsignedInteger('leased_id');
            $table->unsignedInteger('driver_id');
            $table->date('hiring_date');
            $table->date('valid_until')->nullable();
            $table->unsignedInteger('is_active');
            $table->timestamps();

            $table->foreign('leased_id')->references('id')->on('leased');
            $table->foreign('driver_id')->references('id')->on('drivers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chassis');
    }
}

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
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',45);
            $table->string('last_name',45);
            $table->string('cdl_number',45);
            $table->string('phone_number',45);
            $table->string('address',200);
            $table->tinyInteger('has_pec')->default(0);
            $table->tinyInteger('has_h2s')->default(0);
            $table->date('cdl_valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->increments('id');
            $table->date('rental_date');
            $table->unsignedDecimal('cost',9,2);
            $table->unsignedDecimal('deposit_amount',9,2);
            $table->tinyInteger('is_paid')->default(0);
            $table->enum('periodicity',['weekly', 'annual',]);
            $table->date('valid_until')->nullable();
            $table->unsignedInteger('trailer_id');
            $table->unsignedInteger('leased_id');
            $table->unsignedInteger('driver_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trailer_id')->references('id')->on('trailers');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityZipLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_zip_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->decimal('latitude', 8, 4);
            $table->decimal('longitude', 8, 4);
            $table->unsignedInteger('zipcode');
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_zip_locations');
    }
}

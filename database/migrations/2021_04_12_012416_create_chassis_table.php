<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChassisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trailer_number',11);
            $table->string('trailer_plate',11);
            $table->string('trailer_type',50);
            $table->string('picture_url',255);
            $table->enum('status',['available', 'repairing', 'rented']);
            $table->date('registration_expiration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspection_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inspection_category_name',45);
            $table->string('inspection_category_options', 255);
            $table->unsignedInteger('position');
            $table->unsignedTinyInteger('editable');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspection_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inspection_item_name');
            $table->unsignedInteger('inspection_category_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspection_rental', function (Blueprint $table) {
            $table->unsignedInteger('rental_id');
            $table->unsignedInteger('inspection_item_id');
            $table->string('option_value', 2000);
            $table->timestamps();

            $table->foreign('inspection_item_id')->references('id')->on('inspection_items');
            $table->foreign('rental_id')->references('id')->on('rentals');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}

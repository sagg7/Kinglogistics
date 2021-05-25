<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipper_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_types');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renames', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->string('job')->nullable();
            $table->string('control_number')->nullable();
            $table->string('customer_reference')->nullable();
            $table->string('bol')->nullable();
            $table->string('tons')->nullable();
            $table->string('po')->nullable();
            $table->string('carrier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('renames');
    }
}

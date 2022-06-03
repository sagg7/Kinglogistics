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
            $table->string('job');
            $table->string('control_number');
            $table->string('customer_reference');
            $table->string('bol');
            $table->string('tons');
            $table->string('po');
            $table->string('carrier');
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

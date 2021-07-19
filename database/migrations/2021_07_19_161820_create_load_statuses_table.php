<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('load_id')->unsigned();
            $table->timestamp('unallocated_timestamp')->nullable();
            $table->timestamp('requested_timestamp')->nullable();
            $table->timestamp('accepted_timestamp')->nullable();
            $table->timestamp('loading_timestamp')->nullable();
            $table->timestamp('to_location_timestamp')->nullable();
            $table->timestamp('arrived_timestamp')->nullable();
            $table->timestamp('unloading_timestamp')->nullable();
            $table->timestamp('finished_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('load_statuses');
    }
}

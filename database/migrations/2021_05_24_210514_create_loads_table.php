<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('load_type_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('shipper_id');
            $table->unsignedBigInteger('load_log_id')->nullable();
            $table->date('date');
            $table->string('control_number');
            $table->string('origin');
            $table->string('origin_coords');
            $table->string('destination');
            $table->string('destination_coords');
            $table->string('customer_name');
            $table->string('customer_po');
            $table->string('customer_reference');
            $table->string('tons')->nullable();
            $table->string('silo_number')->nullable();
            $table->string('container')->nullable();
            $table->double('weight')->nullable();
            $table->double('mileage')->nullable();
            $table->enum('status', ['unallocated', 'requested', 'accepted', 'loading', 'to_location', 'arrived', 'unloading', 'finished']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('load_type_id')->references('id')->on('load_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('load_log_id')->references('id')->on('load_logs')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loads');
    }
}

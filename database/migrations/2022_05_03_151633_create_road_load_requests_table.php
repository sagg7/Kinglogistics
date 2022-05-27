<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoadLoadRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('road_load_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('road_load_id');
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('requestable_id');
            $table->string('requestable_type');
            $table->enum('status', ['requested', 'accepted', 'rejected'])->default('requested');
            $table->timestamps();

            $table->foreign('road_load_id')->references('id')->on('road_loads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('road_load_requests');
    }
}

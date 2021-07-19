<?php

use App\Enums\BoxStatusesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver_id')->unsigned();
            $table->bigInteger('turn_id')->unsigned();

            $table->boolean('have_truck');
            $table->string('truck_number')->nullable();

            $table->boolean('have_chassis');
            $table->bigInteger('chassis_type_id')->unsigned()->nullable();
            $table->string('chassis_number')->nullable();

            $table->boolean('have_box');
            $table->enum('box_status', [BoxStatusesEnum::EMPTY, BoxStatusesEnum::LOADED])->nullable();
            $table->bigInteger('box_type_id')->unsigned()->nullable();
            $table->string('box_number')->nullable();

            $table->timestamps();
            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');

            $table->foreign('turn_id')
                ->references('id')
                ->on('turns')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}

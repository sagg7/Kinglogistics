<?php

use App\Enums\LoadStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->string('latitude');
            $table->string('longitude');
            $table->enum('status', [
                    LoadStatusEnum::UNALLOCATED,
                    LoadStatusEnum::REQUESTED,
                    LoadStatusEnum::ACCEPTED,
                    LoadStatusEnum::LOADING,
                    LoadStatusEnum::TO_LOCATION,
                    LoadStatusEnum::ARRIVED,
                    LoadStatusEnum::UNLOADING,
                    LoadStatusEnum::FINISHED,
                ]
            );
            $table->bigInteger('load_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->timestamps();

            $table->foreign('load_id')
                ->references('id')
                ->on('loads')
                ->onDelete('cascade');

            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
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
        Schema::dropIfExists('driver_locations');
    }
}

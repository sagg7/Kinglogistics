<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trailer_id');
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('driver_id');
            $table->date('date');
            $table->double('cost');
            $table->double('deposit')->nullable();
            $table->unsignedTinyInteger('deposit_is_paid')->nullable();
            $table->enum('period', ['weekly', 'monthly', 'annual']);
            $table->enum('status', ['uninspected', 'rented', 'finished']);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trailer_id')->references('id')->on('trailers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rentals');
    }
}

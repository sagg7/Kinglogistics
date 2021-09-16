<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusCarrierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_carrier', function (Blueprint $table) {
            $table->unsignedBigInteger('carrier_id')->index();
            $table->unsignedBigInteger('bonus_id')->index();
            $table->unsignedBigInteger('carrier_payment_id')->nullable();

            $table->primary(['carrier_id', 'bonus_id']);
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('bonus_id')->references('id')->on('bonuses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_payment_id')->references('id')->on('carrier_payments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_carrier');
    }
}

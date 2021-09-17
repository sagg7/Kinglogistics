<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_type_id');
            $table->unsignedBigInteger('carrier_payment_id')->nullable()->comment('When carriers relationship is null, it means it\'s for all carriers, thus the carrier payment id is stored in the main table, otherwise the relations is on the pivot table');
            $table->double('amount');
            $table->string('description', 512)->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('bonus_type_id')->references('id')->on('bonus_types')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('bonuses');
    }
}

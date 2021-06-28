<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id');
            $table->unsignedBigInteger('carrier_payment_id')->nullable();
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->double('amount');
            $table->string('description', 512)->nullable();
            $table->enum('type', ['other', 'diesel', 'salary', 'repairments']);
            $table->double('mileage');
            $table->double('gallons');
            $table->unsignedTinyInteger('non_editable')->nullable();
            $table->timestamps();

            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('carrier_payment_id')->references('id')->on('carrier_payments')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('expenses');
    }
}

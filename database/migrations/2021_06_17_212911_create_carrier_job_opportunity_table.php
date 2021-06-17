<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierJobOpportunityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_job_opportunity', function (Blueprint $table) {
            $table->unsignedBigInteger('carrier_id')->index();
            $table->unsignedBigInteger('job_opportunity_id')->index();

            $table->primary(['carrier_id', 'job_opportunity_id']);
            $table->foreign('carrier_id')->references('id')->on('carriers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('job_opportunity_id')->references('id')->on('job_opportunities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrier_job_opportunity');
    }
}

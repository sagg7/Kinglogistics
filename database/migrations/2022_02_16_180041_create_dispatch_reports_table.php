<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_id');
            $table->timestamp('date');
            $table->integer('active_loads');
            $table->integer('active_drivers');
            $table->integer('inactive_drivers');
            $table->string('well_status', 512)->nullable();
            $table->integer('loads_finalized');
            $table->decimal('worked_time');
            $table->decimal('dispatch_score');
            $table->decimal('score_app_usage');
            $table->string('description', 512)->nullable();
            $table->timestamps();
            $table->foreign('dispatch_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispatch_reports');
    }
}

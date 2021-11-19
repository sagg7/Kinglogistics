<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BotAnwswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('bot_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('bot_question_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->string('answer')->nullable();
            $table->integer('incorrect')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_bot_sender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

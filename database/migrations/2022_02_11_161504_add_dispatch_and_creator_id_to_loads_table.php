<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDispatchAndCreatorIdToLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->unsignedBigInteger('dispatch')->after('box_number_end')->nullable();
            $table->unsignedBigInteger('creator_id')->after('dispatch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn(['dispatch']);
            $table->dropColumn(['creator_id']);
        });
    }
}

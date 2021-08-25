<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLoadIdFieldNullableInDriverLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_locations', function (Blueprint $table) {
            $table->bigInteger('load_id')->unsigned()->nullable()->change();

//            $table->foreign('load_id')
//                ->references('id')
//                ->on('loads')
//                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_locations', function (Blueprint $table) {
            $table->bigInteger('load_id')->unsigned()->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimezoneIdToShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('timezone_id')->after('turn_id')->nullable();

            $table->foreign('timezone_id')->references('id')->on('timezones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign('loads_timezone_id_foreign');
            $table->dropIndex('loads_timezone_id_foreign');
            $table->dropColumn('timezone_id');
        });
    }
}

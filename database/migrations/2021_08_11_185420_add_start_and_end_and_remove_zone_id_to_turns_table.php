<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartAndEndAndRemoveZoneIdToTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->dropForeign('turns_zone_id_foreign');
            $table->dropIndex('turns_zone_id_foreign');
            $table->dropColumn('zone_id');
            $table->time('start')->after('name')->nullable();
            $table->time('end')->after('start')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->dropColumn(['start', 'end']);
        });
    }
}

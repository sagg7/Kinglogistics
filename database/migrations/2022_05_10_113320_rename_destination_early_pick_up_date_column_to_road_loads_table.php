<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDestinationEarlyPickUpDateColumnToRoadLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('road_loads', function (Blueprint $table) {
            $table->renameColumn('destination_late_pick_up_date', 'destination_late_drop_off_date');
            $table->renameColumn('destination_early_pick_up_date', 'destination_early_drop_off_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('road_loads', function (Blueprint $table) {
            $table->renameColumn('destination_late_drop_off_date', 'destination_late_pick_up_date');
            $table->renameColumn('destination_early_drop_off_date', 'destination_early_pick_up_date');
        });
    }
}

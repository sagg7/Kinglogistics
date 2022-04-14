<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRoadLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('road_loads', function (Blueprint $table) {
            $table->timestamp('origin_late_pick_up_date');
            $table->timestamp('destination_late_pick_up_date');
            $table->timestamp('origin_early_pick_up_date');
            $table->timestamp('destination_early_pick_up_date');
            $table->unsignedBigInteger('trailer_types_id')->nullable();
            $table->unsignedBigInteger('road_loads_mode_id')->nullable();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('cube');
            $table->unsignedInteger('pieces');
            $table->unsignedInteger('pallets');
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
            $table->dropColumn('origin_late_pick_up_date');
            $table->dropColumn('destination_late_pick_up_date');
            $table->dropColumn('origin_early_pick_up_date');
            $table->dropColumn('destination_early_pick_up_date');
            $table->dropColumn('trailer_types_id')->nullable();
            $table->dropColumn('road_loads_mode_id')->nullable();
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('cube');
            $table->dropColumn('pieces');
            $table->dropColumn('pallets');
        });
    }
}

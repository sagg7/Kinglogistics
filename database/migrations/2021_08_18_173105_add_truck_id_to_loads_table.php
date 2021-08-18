<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTruckIdToLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->unsignedBigInteger('truck_id')->after('driver_id')->nullable();

            $table->foreign('truck_id')->references('id')->on('trucks')->onUpdate('cascade')->onDelete('cascade');
        });
        $loads = \App\Models\Load::join('drivers', 'drivers.id', '=', 'loads.driver_id')
            ->join('trucks', 'trucks.driver_id', '=', 'drivers.id')
            ->update([
                'truck_id' => \Illuminate\Support\Facades\DB::raw('trucks.id'),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn('truck_id');
        });
    }
}

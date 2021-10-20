<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndStatusCurrentAndStatusTotalToTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->enum('status', ['stage', 'loads'])->nullable()->after('mileage');
            $table->double('status_current')->nullable()->after('status');
            $table->double('status_total')->nullable()->after('status_current');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_current', 'status_total']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateTypeToShipperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->enum('type_rate', ['mileage', 'mileage-tons'])->after('loads_per_invoice')->default('mileage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->dropColumn('type_rate');
        });
    }
}

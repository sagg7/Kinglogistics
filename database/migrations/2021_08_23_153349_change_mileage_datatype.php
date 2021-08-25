<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMileageDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            DB::statement("ALTER TABLE loads MODIFY weight DECIMAL(9,2)");
            DB::statement("ALTER TABLE loads MODIFY mileage DECIMAL(9,2)");
            DB::statement("ALTER TABLE loads MODIFY rate DECIMAL(9,2)");
            DB::statement("ALTER TABLE loads MODIFY shipper_rate DECIMAL(9,2)");
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
            //
        });
    }
}

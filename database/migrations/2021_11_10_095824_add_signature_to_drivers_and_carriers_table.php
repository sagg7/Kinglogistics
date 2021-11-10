<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignatureToDriversAndCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('signature', 512)->after('inactive')->nullable();
        });
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('signature', 512)->after('inactive')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('signature');
        });
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('signature');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipperAndFileToPaperworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paperwork', function (Blueprint $table) {
            $table->unsignedBigInteger('shipper_id')->after('id')->nullable();
            $table->string('file', 512)->after('type')->nullable();

            $table->foreign('shipper_id')->references('id')->on('shippers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paperwork', function (Blueprint $table) {
            $table->dropColumn(['shipper_id', 'file']);
        });
    }
}

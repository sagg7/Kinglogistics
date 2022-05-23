<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginIdAndDestinationIdToLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_id')->nullable()->after('load_log_id');
            $table->unsignedBigInteger('destination_id')->nullable()->after('origin_id');

            $table->foreign('origin_id')->references('id')->on('origins')->onUpdate('cascade');
            $table->foreign('destination_id')->references('id')->on('destinations')->onUpdate('cascade');
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
            $table->dropColumn(['origin_id', 'destination_id']);
        });
    }
}

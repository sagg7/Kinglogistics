<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordIdAndDestinationIdOnTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_id')->nullable()->after('rate_id');
            $table->unsignedBigInteger('destination_id')->nullable()->after('origin_id');

            $table->string('origin')->nullable()->change();
            $table->string('origin_coords')->nullable()->change();
            $table->string('destination')->nullable()->change();
            $table->string('destination_coords')->nullable()->change();

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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('origin')->nullable();
            $table->string('origin_coords')->nullable();
            $table->string('destination')->nullable();
            $table->string('destination_coords')->nullable();
            $table->dropColumn(['origin_id', 'destination_id']);
        });
    }
}

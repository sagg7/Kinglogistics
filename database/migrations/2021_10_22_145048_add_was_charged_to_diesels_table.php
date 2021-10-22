<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWasChargedToDieselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diesels', function (Blueprint $table) {
            $table->dropForeign('diesels_carrier_payment_id_foreign');
            $table->dropIndex('diesels_carrier_payment_id_foreign');

            $table->dropColumn('carrier_payment_id');
            $table->boolean('was_charged')->nullable()->after('truck_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diesels', function (Blueprint $table) {
            $table->dropColumn('was_charged');
            $table->unsignedBigInteger('carrier_payment_id')->after('truck_id')->nullable();
            $table->foreign('carrier_payment_id')->references('id')->on('carrier_payments')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}

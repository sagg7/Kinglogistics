<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerIdToCarrierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->after('id')->nullable();
            $table->boolean('completed_paperwork')->after('signature')->nullable();

            $table->foreign('seller_id')->references('id')->on('users');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('completed_paperwork')->after('signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropForeign('carriers_seller_id_foreign');
            $table->dropIndex('carriers_seller_id_foreign');
            $table->dropColumn('seller_id', 'completed_paperwork');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('seller_id', 'completed_paperwork');
        });
    }
}

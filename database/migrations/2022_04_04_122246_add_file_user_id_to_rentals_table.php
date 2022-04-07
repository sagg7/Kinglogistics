<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileUserIdToRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('charge_date');
            $table->unsignedBigInteger('delivery_user_id')->after('user_id')->nullable();
            $table->unsignedBigInteger('returned_user_id')->after('delivery_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('delivery_user_id');
            $table->dropColumn('returned_user_id');
        });
    }
}

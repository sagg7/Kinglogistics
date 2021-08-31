<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtInspection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_categories', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('inspection_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('inspection_rental_delivery', function (Blueprint $table) {
            $table->string('option_value', 1024)->after('inspection_item_id')->nullable();
        });
        Schema::table('inspection_rental_return', function (Blueprint $table) {
            $table->string('option_value', 1024)->after('inspection_item_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

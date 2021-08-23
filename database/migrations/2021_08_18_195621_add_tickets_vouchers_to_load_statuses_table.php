<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketsVouchersToLoadStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('load_statuses', function (Blueprint $table) {
            $table->string('to_location_voucher', 512)
                ->nullable()
                ->after('finished_timestamp');
            $table->string('finished_voucher', 512)
                ->nullable()
                ->after('to_location_voucher');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('load_statuses', function (Blueprint $table) {
            $table->dropColumn('to_location_voucher');
            $table->dropColumn('finished_voucher');
        });
    }
}

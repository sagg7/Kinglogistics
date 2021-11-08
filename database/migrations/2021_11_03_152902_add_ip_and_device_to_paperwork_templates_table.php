<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpAndDeviceToPaperworkTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paperwork_templates', function (Blueprint $table) {
            $table->string('ip', 12)->after('filled_template')->nullable();
            $table->string('device')->after('ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paperwork_templates', function (Blueprint $table) {
            $table->dropColumn(['ip', 'device']);
        });
    }
}

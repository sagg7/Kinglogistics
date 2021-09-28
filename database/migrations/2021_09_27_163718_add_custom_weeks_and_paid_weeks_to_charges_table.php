<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCustomWeeksAndPaidWeeksToChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE charges MODIFY COLUMN period ENUM('single', 'weekly', 'custom') DEFAULT 'single'");

        Schema::table('charges', function (Blueprint $table) {
            $table->integer('custom_weeks')->nullable()->after('period');
            $table->integer('paid_weeks')->nullable()->after('custom_weeks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE charges MODIFY COLUMN period ENUM('single', 'weekly') DEFAULT 'single'");
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn(['custom_weeks', 'paid_weeks']);
        });
    }
}

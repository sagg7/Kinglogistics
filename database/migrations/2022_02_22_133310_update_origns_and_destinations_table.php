<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrignsAndDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('origins', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->softDeletes();
            $table->enum('status', ['stage', 'loads'])->nullable()->after('coords');
            $table->double('status_current')->nullable()->after('status');
            $table->double('status_total')->nullable()->after('status_current');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('origins', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'status', 'status_current', 'status_total']);
        });
    }
}

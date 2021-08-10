<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMorphFieldsInMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the morphTo columns
            $table->dropColumn('messageable_id');
            $table->dropColumn('messageable_type');

            // Add ref column to users table
            $table->foreignId('user_id')
                ->nullable()
                ->after('driver_id')
                ->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->bigInteger('messageable_id')
                ->unsigned()
                ->nullable();
            $table->string('messageable_type')->nullable();
        });
    }
}

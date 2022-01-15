<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrokerIdToMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('shippers', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('carriers', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('charges', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('bonuses', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('safety_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('trailers', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('trailer_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('trucks', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('paperwork', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('loads', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('load_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('rate_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('job_opportunities', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('expense_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('income_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('bonus_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('charge_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
        });
        Schema::table('incident_types', function (Blueprint $table) {
            $table->unsignedBigInteger('broker_id')->after('id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->onDelete('cascade');
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
            $table->dropForeign('rentals_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('shippers', function (Blueprint $table) {
            $table->dropForeign('shippers_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign('charges_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dropForeign('bonuses_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('safety_messages', function (Blueprint $table) {
            $table->dropForeign('safety_messages_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign('drivers_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('trailers', function (Blueprint $table) {
            $table->dropForeign('trailers_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('trailer_types', function (Blueprint $table) {
            $table->dropForeign('trailer_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropForeign('trucks_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign('zones_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('paperwork', function (Blueprint $table) {
            $table->dropForeign('paperwork_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('loads', function (Blueprint $table) {
            $table->dropForeign('loads_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('load_types', function (Blueprint $table) {
            $table->dropForeign('load_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign('trips_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign('rates_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('rate_groups', function (Blueprint $table) {
            $table->dropForeign('rate_groups_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('job_opportunities', function (Blueprint $table) {
            $table->dropForeign('job_opportunities_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropForeign('expense_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign('incomes_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('income_types', function (Blueprint $table) {
            $table->dropForeign('income_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('bonus_types', function (Blueprint $table) {
            $table->dropForeign('bonus_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('charge_types', function (Blueprint $table) {
            $table->dropForeign('charge_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
        Schema::table('incident_types', function (Blueprint $table) {
            $table->dropForeign('incident_types_broker_id_foreign');
            $table->dropColumn(['broker_id']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierExpenseTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('carrier_expenses', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('type_id')->nullable()->after('truck_id');

            $table->foreign('type_id')->references('id')->on('carrier_expense_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrier_expense_types');

        Schema::table('carrier_expenses', function (Blueprint $table) {
            $table->dropForeign('carrier_expenses_type_id_foreign');
            $table->dropIndex('carrier_expenses_type_id_foreign');
            $table->dropColumn('carrier_expense_type_id');
        });
    }
}

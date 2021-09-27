<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipperInvoiceIdToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('shipper_invoice_id')->nullable()->after('type_id');
            $table->unsignedBigInteger('type_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();

            $table->foreign('shipper_invoice_id')->references('id')->on('shipper_invoices')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_shipper_invoice_id_foreign');
            $table->dropIndex('expenses_shipper_invoice_id_foreign');
            $table->dropColumn('shipper_invoice_id');
        });
    }
}

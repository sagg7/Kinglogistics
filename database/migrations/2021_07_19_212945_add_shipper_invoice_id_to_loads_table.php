<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipperInvoiceIdToLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->unsignedBigInteger('shipper_invoice_id')->after('carrier_payment_id')->nullable();
            $table->double('shipper_rate')->nullable()->after('rate');

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
        Schema::table('loads', function (Blueprint $table) {
            $table->dropColumn(['shipper_invoice_id', 'shipper_rate']);
        });
    }
}

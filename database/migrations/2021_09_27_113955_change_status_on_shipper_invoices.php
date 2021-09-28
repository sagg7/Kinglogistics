<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeStatusOnShipperInvoices extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE shipper_invoices MODIFY COLUMN status ENUM('pending', 'completed', 'paid') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE shipper_invoices MODIFY COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
    }
}

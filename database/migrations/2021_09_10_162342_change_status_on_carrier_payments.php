<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeStatusOnCarrierPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE carrier_payments MODIFY COLUMN status ENUM('pending', 'approved', 'completed', 'charges') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE carrier_payments MODIFY COLUMN status ENUM('pending', 'completed', 'charges') DEFAULT 'pending'");
    }
}

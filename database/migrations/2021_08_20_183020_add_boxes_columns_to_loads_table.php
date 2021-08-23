<?php

use App\Enums\BoxStatusesEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoxesColumnsToLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loads', function (Blueprint $table) {
            $table->enum('box_status_init', [BoxStatusesEnum::EMPTY, BoxStatusesEnum::LOADED])
                ->after('shipper_invoice_id')
                ->nullable();
            $table->bigInteger('box_type_id_init')
                ->after('box_status_init')
                ->unsigned()
                ->nullable();
            $table->string('box_number_init')
                ->after('box_type_id_init')
                ->nullable();

            $table->enum('box_status_end', [BoxStatusesEnum::EMPTY, BoxStatusesEnum::LOADED])
                ->after('box_number_init')
                ->nullable();
            $table->bigInteger('box_type_id_end')
                ->after('box_status_end')
                ->unsigned()
                ->nullable();
            $table->string('box_number_end')
                ->after('box_type_id_end')
                ->nullable();
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
            $table->dropColumn('box_status_init');
            $table->dropColumn('box_type_id_init');
            $table->dropColumn('box_number_init');
            $table->dropColumn('box_status_end');
            $table->dropColumn('box_type_id_end');
            $table->dropColumn('box_number_end');
        });
    }
}

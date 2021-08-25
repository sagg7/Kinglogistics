<?php

use App\Models\ChassisType;
use App\Models\Trailer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChassisTypeIdToTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trailers', function (Blueprint $table) {
            $table->bigInteger('chassis_type_id')
                ->unsigned()
                ->after('shipper_id');
        });

        Schema::table('trailers', function(Blueprint $table){
            // Set a default chassis type, this migration will fail if there is not chassis types entries
            $chassisType = ChassisType::all()->first();

            if (empty($chassisType)) {
                $this->down();
                return;
            }

            Trailer::all()->each(function ($trailer) use($chassisType) {
                $trailer->chassis_type_id = $chassisType->id;
                $trailer->update();
            });

            $table->foreign('chassis_type_id')
                ->references('id')
                ->on('chassis_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trailers', function (Blueprint $table) {
            $table->dropColumn('chassis_type_id');
        });
    }
}

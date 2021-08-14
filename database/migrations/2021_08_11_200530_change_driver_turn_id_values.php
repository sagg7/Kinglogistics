<?php

use App\Models\Driver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDriverTurnIdValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => 'TurnsSeeder',
        ]);
        Driver::where('turn_id', 1)
            ->update(['turn_id' => 2]);
        Driver::where('turn_id', 0)
            ->update(['turn_id' => 1]);
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreign('turn_id')->references('id')->on('turns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            Driver::where('turn_id', 1)
                ->update(['turn_id' => 0]);
            Driver::where('turn_id', 2)
                ->update(['turn_id' => 1]);
            $table->dropForeign('drivers_turn_id_foreign');
            $table->dropIndex('drivers_turn_id_foreign');
        });
    }
}

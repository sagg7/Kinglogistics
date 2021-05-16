<?php

namespace Database\Seeders;

use App\Models\TrailerType;
use Illuminate\Database\Seeder;

class TrailerTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new TrailerType();

        $type->name = "Sandbox Chassis";
        $type->carrier_id = 1;
        $type->save();

        $type = new TrailerType();

        $type->name = "CIG";
        $type->carrier_id = 1;
        $type->save();

        $type = new TrailerType();

        $type->name = "HI-Crush";
        $type->carrier_id = 1;
        $type->save();
    }
}

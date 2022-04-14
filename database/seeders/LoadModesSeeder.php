<?php

namespace Database\Seeders;

use App\Models\LoadMode;
use Illuminate\Database\Seeder;

class LoadModesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = new LoadMode();
        $item->name = "Truck Load";
        $item->save();
        $item = new LoadMode();
        $item->name = "Less Than Truck Load";
        $item->save();
        $item = new LoadMode();
        $item->name = "Intermodal";
        $item->save();
        $item = new LoadMode();
        $item->name = "Partial";
        $item->save();
        $item = new LoadMode();
        $item->name = "Drayage";
        $item->save();
        $item = new LoadMode();
        $item->name = "Parcel";
        $item->save();
        $item = new LoadMode();
        $item->name = "Air";
        $item->save();
        $item = new LoadMode();
        $item->name = "Water";
        $item->save();
        $item = new LoadMode();
        $item->name = "Ocean";
        $item->save();
    }
}

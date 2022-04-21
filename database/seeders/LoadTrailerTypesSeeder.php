<?php

namespace Database\Seeders;

use App\Models\LoadTrailerType;
use Illuminate\Database\Seeder;

class LoadTrailerTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = new LoadTrailerType();
        $item->name = "Dry Van";
        $item->abbreviation = "V";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Flatbed";
        $item->abbreviation = "F";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Reefer";
        $item->abbreviation = "R";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Step Deck/Single Drop";
        $item->abbreviation = "SD";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Double Drop";
        $item->abbreviation = "DD";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Removable Gooseneck";
        $item->abbreviation = "RGN";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Maxi Flat";
        $item->abbreviation = "MX";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Van+Vented";
        $item->abbreviation = "VV";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Van+Airride";
        $item->abbreviation = "VA";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Van+Intermodal";
        $item->abbreviation = "VINT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Flat+Intermodal";
        $item->abbreviation = "FINT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Reefer+Intermodal";
        $item->abbreviation = "RINT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Curtain Van";
        $item->abbreviation = "CV";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Container";
        $item->abbreviation = "CONT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Hotshot";
        $item->abbreviation = "HS";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Cargo Van";
        $item->abbreviation = "CRG";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Dump Trailer";
        $item->abbreviation = "DT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Auto Carrier";
        $item->abbreviation = "AC";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Hopper Bottom";
        $item->abbreviation = "HB";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Tanker";
        $item->abbreviation = "TNK";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Lowboy";
        $item->abbreviation = "LB";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Landall";
        $item->abbreviation = "LA";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Flat+Sides";
        $item->abbreviation = "FS";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Flat+Tarp";
        $item->abbreviation = "FT";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Power Only";
        $item->abbreviation = "PO";
        $item->save();
        $item = new LoadTrailerType();
        $item->name = "Other";
        $item->abbreviation = "O";
        $item->save();
    }
}

<?php

namespace Database\Seeders;

use App\Models\InspectionItem;
use Illuminate\Database\Seeder;

class InspectionItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = new InspectionItem();
        $item->inspection_category_id = 1;
        $item->name = "Red Glad hand";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 1;
        $item->name = "Blue Glad hand";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 1;
        $item->name = "Electrical Connector";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 1;
        $item->name = "Documents / Updated";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Front left";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Front right";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Middle left";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Middle right";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back left";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back right";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back left 1";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back left 2";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back left 3";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back right 1";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back right 2";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back right 3";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "ABS";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Front left inside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Front left outside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Back left inside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Back left outside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Front right inside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Front right outside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Back right inside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 3;
        $item->name = "Back right outside";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 9;
        $item->name = "FL brake pad";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 9;
        $item->name = "BL brake pad";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 9;
        $item->name = "FR brake pad";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 9;
        $item->name = "BR brake pad";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Front Left Airbag";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Back Left Airbag";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Front Rigth Airbag";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Back Rigth Airbag";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Front Left Chamber";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Back Left Chamber";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Front Rigth Chamber";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 4;
        $item->name = "Back Rigth Chamber";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 5;
        $item->name = "Body contitions";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 6;
        $item->name = "Comments";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 7;
        $item->name = "Inspector signature";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 8;
        $item->name = "Carrier signature";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Plate Light";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back center 1";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back center 2";
        $item->save();

        $item = new InspectionItem();
        $item->inspection_category_id = 2;
        $item->name = "Back center 3";
        $item->save();
    }
}

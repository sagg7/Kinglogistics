<?php

namespace Database\Seeders;

use App\Models\InspectionCategory;
use Illuminate\Database\Seeder;

class InspectionCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new InspectionCategory();
        $category->name = "Gadgets";
        $category->options = json_encode([
            "type" => "options",
            "options" =>["Good", "Damaged"],
            "default" => "Good"
        ]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Lights";
        $category->options = json_encode([
            "type" => "options",
            "options" => [ "Good", "Damaged"],
            "default" =>  "Good"
        ]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Tires";
        $category->options = json_encode([
            "type" => "inputs",
            "options" => ["mm" , "size"],
            "default" => ["24" , " 295/75R"]
        ]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Damping system";
        $category->options = json_encode([
            "type" => "options",
            "options" => ["Good", "Damaged"],
            "default" => "Good"
        ]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Body conditions";
        $category->options = json_encode(["type" => "coords"]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Comments";
        $category->options = json_encode(["type" => "text-area"]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Inspector signature";
        $category->options = json_encode(["type" => "base64"]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Carrier signature";
        $category->options = json_encode(["type" => "base64"]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();

        $category = new InspectionCategory();
        $category->name = "Brakes";
        $category->options = json_encode([
            "type" => "inputs",
            "options" => ["mm"],
            "default" => ["12"]
        ]);
        $category->position = 1;
        $category->editable = 1;
        $category->save();
    }
}

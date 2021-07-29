<?php

namespace Database\Seeders;

use App\Models\BoxType;
use App\Models\ChassisType;
use Illuminate\Database\Seeder;

class BoxTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boxes = [
            "SBCH",
            "PTCH",
            "SBLD",
            "SBXT",
        ];

        foreach ($boxes as $box) {
            BoxType::create(["name" => $box]);
        }
    }
}

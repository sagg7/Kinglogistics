<?php

namespace Database\Seeders;

use App\Models\ChassisType;
use Illuminate\Database\Seeder;

class ChassisTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chassis = [
            "SBCH",
            "PTCH",
            "SBLD",
            "SBXT",
        ];

        foreach ($chassis as $c) {
            ChassisType::create(['name' => $c]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\TrailerType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Minum required config for app functionality
        $this->call(AppConfigsSeeder::class);
        $this->call(BoxTypesSeeder::class);
        $this->call(ChassisTypesSeeder::class);


        // \App\Models\User::factory(10)->create();
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(TrailerTypesSeeder::class);
        $this->call(CarrierTestSeeder::class);
        $this->call(TurnsSeeder::class);
        $this->call(InspectionCategoriesSeeder::class);
        $this->call(InspectionItemsSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\AppConfigEnum;
use App\Models\AppConfig;
use Illuminate\Database\Seeder;

class AppConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // In seconds
        AppConfig::create([
            'key' => AppConfigEnum::LOAD_THRESHOLD,
            'value' => 900
        ]);

        // In seconds
        AppConfig::create([
            'key' => AppConfigEnum::LOCATION_UPDATE_FRECUENCY,
            'value' => 30
        ]);

        // Rejection times
        AppConfig::create([
            'key' => AppConfigEnum::MAX_LOAD_REJECTIONS,
            'value' => 3
        ]);

        // Rejection times
        AppConfig::create([
            'key' => AppConfigEnum::TIME_AFTER_LOAD_REMINDER,
            'value' => 3
        ]);
    }
}

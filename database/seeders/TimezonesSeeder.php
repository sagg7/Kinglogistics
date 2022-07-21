<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;

class TimezonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezone = new Timezone();
        $timezone->name = "Pacific Time";
        $timezone->abbreviation = "PT";
        $timezone->php_timezone = "America/Los_Angeles";
        $timezone->save();

        $timezone = new Timezone();
        $timezone->name = "Mountain Time";
        $timezone->abbreviation = "MT";
        $timezone->php_timezone = "America/Denver";
        $timezone->save();

        $timezone = new Timezone();
        $timezone->name = "Mountain Time (no DST)";
        $timezone->abbreviation = "MT";
        $timezone->php_timezone = "America/Phoenix";
        $timezone->save();

        $timezone = new Timezone();
        $timezone->name = "Central Time";
        $timezone->abbreviation = "CT";
        $timezone->php_timezone = "America/Chicago";
        $timezone->save();

        $timezone = new Timezone();
        $timezone->name = "Eastern Time";
        $timezone->abbreviation = "ET";
        $timezone->php_timezone = "America/New_York";
        $timezone->save();
    }
}

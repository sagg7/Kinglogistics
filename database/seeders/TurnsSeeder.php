<?php

namespace Database\Seeders;

use App\Models\Turn;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TurnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        Turn::insert([
            [
                'name' => 'Morning',
                'start' => '6:00',
                'end' => '17:59',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Night',
                'start' => '18:00',
                'end' => '5:59',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

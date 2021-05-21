<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CarrierTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carrier = new Carrier();
        $carrier->name = "Test Carrier";
        $carrier->email = "carrier@test.com";
        $carrier->password = Hash::make('carrier.1234');
        $carrier->save();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Developer";
        $user->email = "developer@kinglogisticoil.com";
        $user->password = Hash::make("dEve.6732");
        $user->save();

        $user->roles()->sync([1]); // Set admin role
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrator Role
        $admin_role = new Role();
        $admin_role->slug = 'admin';
        $admin_role->name = 'Administrator';
        $admin_role->save();

        // Accountant Role
        $assistant_role = new Role();
        $assistant_role->slug = 'accountant';
        $assistant_role->name = 'Accountant';
        $assistant_role->save();

        // Seller Role
        $doctor_role = new Role();
        $doctor_role->slug = 'seller';
        $doctor_role->name = 'Seller';
        $doctor_role->save();

        // Safety Role
        $doctor_role = new Role();
        $doctor_role->slug = 'safety';
        $doctor_role->name = 'Safety';
        $doctor_role->save();

        // Operations Role
        $doctor_role = new Role();
        $doctor_role->slug = 'operations';
        $doctor_role->name = 'Operations';
        $doctor_role->save();

        // Dispatch Role
        $doctor_role = new Role();
        $doctor_role->slug = 'dispatch';
        $doctor_role->name = 'Dispatch';
        $doctor_role->save();

        // Dispatch Role
        $doctor_role = new Role();
        $doctor_role->slug = 'spotter';
        $doctor_role->name = 'Spotter';
        $doctor_role->save();

        // Dispatch Role
        $doctor_role = new Role();
        $doctor_role->slug = 'hr';
        $doctor_role->name = 'Human Resources';
        $doctor_role->save();
    }
}

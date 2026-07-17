<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@travelbox.my'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $admin->assignRole('super_admin');

        User::updateOrCreate(
            ['email' => 'manager@travelbox.my'],
            ['name' => 'Manager', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        )->assignRole('manager');

        User::updateOrCreate(
            ['email' => 'accountant@travelbox.my'],
            ['name' => 'Accountant', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        )->assignRole('accountant');

        $this->call([
            ChartOfAccountSeeder::class,
            DefaultSettingsSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            TripSeeder::class,
            PassengerSeeder::class,
        ]);
    }
}

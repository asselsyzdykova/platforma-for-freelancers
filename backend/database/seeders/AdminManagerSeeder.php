<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@bezrab.local');
        $adminPassword = env('ADMIN_PASSWORD', 'Admin123!');

        $managerEmail = env('MANAGER_EMAIL', 'manager@bezrab.local');
        $managerPassword = env('MANAGER_PASSWORD', 'Manager123!');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Main Admin',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => $managerEmail],
            [
                'name' => 'Manager',
                'password' => Hash::make($managerPassword),
                'role' => 'manager',
            ]
        );
    }
}

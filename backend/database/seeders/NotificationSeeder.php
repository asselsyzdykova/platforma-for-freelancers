<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::all() as $user) {
            Notification::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['user_id' => $user->id]);
        }
    }
}

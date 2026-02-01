<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freelancers = User::where('role', 'freelancer')->get();

        foreach ($freelancers as $freelancer) {
            Subscription::factory()->create([
                'user_id' => $freelancer->id,
            ]);
        }
    }
}

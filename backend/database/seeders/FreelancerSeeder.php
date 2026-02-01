<?php

namespace Database\Seeders;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class FreelancerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freelancers = User::factory()
            ->count(10)
            ->create(['role' => 'freelancer']);

        foreach ($freelancers as $freelancer) {
            FreelancerProfile::factory()->create([
                'user_id' => $freelancer->id,
            ]);
        }
    }
}

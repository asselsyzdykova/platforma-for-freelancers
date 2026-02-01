<?php

namespace Database\Seeders;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FreelancerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $knownFreelancers = [
            [
                'user' => [
                    'name' => 'Martin Svoboda',
                    'email' => 'freelancer.martin@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'freelancer',
                ],
                'profile' => [
                    'about' => 'Full-stack developer specializing in Laravel and Vue.',
                    'location' => 'Brno',
                    'skills' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'REST API'],
                    'completed_projects' => 18,
                    'proposals' => 12,
                    'rating' => 4.9,
                    'reviews' => 27,
                    'avatar' => null,
                ],
            ],
            [
                'user' => [
                    'name' => 'Eva Horakova',
                    'email' => 'freelancer.eva@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'freelancer',
                ],
                'profile' => [
                    'about' => 'UI/UX designer focused on product design and usability.',
                    'location' => 'Prague',
                    'skills' => ['UI/UX', 'Figma', 'Design Systems', 'Prototyping'],
                    'completed_projects' => 22,
                    'proposals' => 9,
                    'rating' => 4.8,
                    'reviews' => 31,
                    'avatar' => null,
                ],
            ],
            [
                'user' => [
                    'name' => 'Tomas Novak',
                    'email' => 'freelancer.tomas@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'freelancer',
                ],
                'profile' => [
                    'about' => 'Data analyst and Python developer with strong SQL skills.',
                    'location' => 'Bratislava',
                    'skills' => ['Python', 'Data Analysis', 'Pandas', 'SQL', 'Visualization'],
                    'completed_projects' => 14,
                    'proposals' => 15,
                    'rating' => 4.6,
                    'reviews' => 19,
                    'avatar' => null,
                ],
            ],
        ];

        foreach ($knownFreelancers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            FreelancerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );
        }

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

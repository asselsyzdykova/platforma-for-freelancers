<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $knownClients = [
            [
                'user' => [
                    'name' => 'Alice Novak',
                    'email' => 'client.alice@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'client',
                ],
                'profile' => [
                    'company' => 'Novak Consulting',
                    'location' => 'Bratislava',
                    'about' => 'Looking for reliable freelancers for web and mobile projects.',
                    'avatar' => null,
                    'rating' => 4.8,
                    'reviews' => 34,
                ],
            ],
            [
                'user' => [
                    'name' => 'Peter Kral',
                    'email' => 'client.peter@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'client',
                ],
                'profile' => [
                    'company' => 'Kral Studio',
                    'location' => 'Kosice',
                    'about' => 'Branding and design projects for startups.',
                    'avatar' => null,
                    'rating' => 4.5,
                    'reviews' => 12,
                ],
            ],
            [
                'user' => [
                    'name' => 'Lucia Havel',
                    'email' => 'client.lucia@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'client',
                ],
                'profile' => [
                    'company' => 'Havel Marketing',
                    'location' => 'Zilina',
                    'about' => 'Marketing and SEO campaigns with clear KPIs.',
                    'avatar' => null,
                    'rating' => 4.7,
                    'reviews' => 20,
                ],
            ],
        ];

        foreach ($knownClients as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            ClientProfile::updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );
        }

        $clients = User::factory()
            ->count(6)
            ->create(['role' => 'client']);

        foreach ($clients as $client) {
            ClientProfile::factory()->create([
                'user_id' => $client->id,
            ]);
        }
    }
}

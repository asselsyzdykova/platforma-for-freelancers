<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

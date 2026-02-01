<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freelancerIds = User::where('role', 'freelancer')->pluck('id');

        foreach (Project::all() as $project) {
            $count = min(fake()->numberBetween(1, 4), $freelancerIds->count());
            $selectedIds = $freelancerIds->shuffle()->take($count);

            foreach ($selectedIds as $freelancerId) {
                Proposal::factory()->create([
                    'project_id' => $project->id,
                    'freelancer_id' => $freelancerId,
                ]);
            }
        }
    }
}

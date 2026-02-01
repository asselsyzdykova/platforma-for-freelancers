<?php

namespace Database\Factories;

use App\Models\FreelancerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FreelancerProfile>
 */
class FreelancerProfileFactory extends Factory
{
    protected $model = FreelancerProfile::class;

    public function definition(): array
    {
        $skillsPool = [
            'Web Development',
            'UI/UX',
            'PHP',
            'Laravel',
            'Vue.js',
            'JavaScript',
            'Python',
            'Data Analysis',
            'SEO',
            'Content Writing',
            'Mobile Apps',
            'Graphic Design',
        ];

        return [
            'about' => $this->faker->paragraphs(2, true),
            'location' => $this->faker->city(),
            'skills' => $this->faker->randomElements($skillsPool, $this->faker->numberBetween(3, 6)),
            'completed_projects' => $this->faker->numberBetween(0, 40),
            'proposals' => $this->faker->numberBetween(0, 80),
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'reviews' => $this->faker->numberBetween(0, 120),
            'avatar' => null,
        ];
    }
}

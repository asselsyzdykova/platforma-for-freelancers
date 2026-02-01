<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $categories = ['Design', 'Development', 'Marketing', 'Writing', 'Data', 'Product'];
        $tagsPool = ['web', 'ui', 'backend', 'frontend', 'seo', 'api', 'mobile', 'branding', 'research'];

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(3, true),
            'budget' => '$' . $this->faker->numberBetween(200, 5000),
            'category' => $this->faker->randomElement($categories),
            'tags' => $this->faker->randomElements($tagsPool, $this->faker->numberBetween(2, 4)),
            'status' => $this->faker->randomElement(['In progress', 'Completed', 'Open']),
            'deadline' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
        ];
    }
}

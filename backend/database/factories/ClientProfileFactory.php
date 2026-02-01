<?php

namespace Database\Factories;

use App\Models\ClientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    protected $model = ClientProfile::class;

    public function definition(): array
    {
        return [
            'company' => $this->faker->company(),
            'location' => $this->faker->city(),
            'about' => $this->faker->paragraphs(2, true),
            'avatar' => null,
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'reviews' => $this->faker->numberBetween(0, 120),
        ];
    }
}

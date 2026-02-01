<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'plan' => $this->faker->randomElement(['basic', 'pro', 'premium']),
            'provider' => 'stripe',
            'provider_id' => (string) Str::uuid(),
            'status' => $this->faker->randomElement(['active', 'canceled', 'expired']),
            'start_date' => $startDate,
            'end_date' => $this->faker->optional()->dateTimeBetween($startDate, '+3 months'),
        ];
    }
}

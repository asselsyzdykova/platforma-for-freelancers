<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $types = ['proposal', 'project', 'message', 'subscription'];

        return [
            'type' => $this->faker->randomElement($types),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->sentence(12),
            'link' => $this->faker->optional()->randomElement(['/projects', '/freelancer-profile', '/client-profile']),
            'is_read' => $this->faker->boolean(25),
        ];
    }
}

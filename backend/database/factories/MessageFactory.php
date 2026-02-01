<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'body' => $this->faker->sentence(12),
            'read_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}

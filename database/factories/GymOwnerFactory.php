<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymOwnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gym_id' => Gym::factory(),
            'owner_code' => 'OWN' . strtoupper(fake()->unique()->numerify('####')),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-25 years'),
            'address' => fake()->address(),
            'joining_date' => fake()->dateTimeBetween('-5 years', 'now'),
        ];
    }
}

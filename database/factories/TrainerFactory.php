<?php

namespace Database\Factories;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gym_id' => Gym::factory(),
            'trainer_code' => 'TR' . strtoupper(fake()->unique()->numerify('####')),
            'specialization' => fake()->randomElement(['Strength', 'Cardio', 'Yoga', 'CrossFit', 'Boxing']),
            'qualification' => fake()->randomElement(['B.Sc Sports Science', 'ACE Certified', 'NASM CPT', 'ISSA Certified']),
            'experience_years' => fake()->numberBetween(1, 15),
            'joining_date' => fake()->dateTimeBetween('-5 years', 'now'),
        ];
    }
}

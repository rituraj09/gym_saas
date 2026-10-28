<?php

namespace Database\Factories;

use App\Models\GymStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Gym',
            'code' => 'GYM' . strtoupper(fake()->unique()->lexify('???')),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+1##########'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip_code' => fake()->postcode(),
            'gym_status_id' => GymStatus::where('code', 'active')->first()?->id,
            'established_date' => fake()->dateTimeBetween('-10 years', 'now'),
        ];
    }
}

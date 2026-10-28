<?php

namespace Database\Factories;

use App\Models\BloodGroupType;
use App\Models\GenderType;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gym_id' => Gym::factory(),
            'member_code' => 'M' . strtoupper(fake()->unique()->numerify('#####')),
            'date_of_birth' => fake()->dateTimeBetween('-45 years', '-16 years'),
            'gender_id' => GenderType::inRandomOrder()->first()?->id,
            'blood_group_id' => BloodGroupType::inRandomOrder()->first()?->id,
            'address' => fake()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('+1##########'),
            'joining_date' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}

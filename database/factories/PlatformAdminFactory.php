<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformAdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_code' => 'ADM' . strtoupper(fake()->unique()->numerify('####')),
        ];
    }
}

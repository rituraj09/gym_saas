<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $actions = ['created', 'updated', 'deleted'];
        $models = ['Trainer', 'Member', 'Membership', 'GymMemberPayment'];
        $model = fake()->randomElement($models);

        return [
            'gym_id'      => Gym::inRandomOrder()->first()?->id,
            'user_id'     => User::inRandomOrder()->first()?->id,
            'action'      => fake()->randomElement($actions),
            'model_type'  => "App\\Models\\{$model}",
            'model_id'    => fake()->numberBetween(1, 50),
            'model_label' => fake()->name(),
            'old_values'  => $model === 'updated' ? ['phone' => '111-222-3333'] : null,
            'new_values'  => in_array($model, ['created', 'updated']) ? ['phone' => '444-555-6666'] : null,
            'ip_address'  => fake()->ipv4(),
            'user_agent'  => fake()->userAgent(),
        ];
    }
}

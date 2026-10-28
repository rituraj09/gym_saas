<?php

namespace Database\Seeders;

use App\Models\SaasPlan;
use Illuminate\Database\Seeder;

class SaasPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'code' => 'basic',
                'description' => 'Perfect for small gyms just getting started.',
                'monthly_price' => 29.99,
                'yearly_price' => 299.99,
                'max_trainers' => 3,
                'max_members' => 50,
                'has_exercise_module' => true,
                'has_diet_module' => false,
                'has_reports_module' => false,
                'has_priority_support' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'code' => 'pro',
                'description' => 'For growing gyms that need full features.',
                'monthly_price' => 79.99,
                'yearly_price' => 799.99,
                'max_trainers' => 10,
                'max_members' => 200,
                'has_exercise_module' => true,
                'has_diet_module' => true,
                'has_reports_module' => true,
                'has_priority_support' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'description' => 'Unlimited everything for large gym chains.',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.99,
                'max_trainers' => 0,
                'max_members' => 0,
                'has_exercise_module' => true,
                'has_diet_module' => true,
                'has_reports_module' => true,
                'has_priority_support' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SaasPlan::updateOrCreate(
                ['code' => $plan['code']],
                array_merge($plan, ['is_active' => true])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MealType;
use Illuminate\Database\Seeder;

class MealTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Pre-Workout',  'code' => 'pre_workout',  'sort_order' => 1],
            ['name' => 'Breakfast',    'code' => 'breakfast',    'sort_order' => 2],
            ['name' => 'Mid-Morning',  'code' => 'mid_morning',  'sort_order' => 3],
            ['name' => 'Lunch',        'code' => 'lunch',        'sort_order' => 4],
            ['name' => 'Afternoon',    'code' => 'afternoon',    'sort_order' => 5],
            ['name' => 'Post-Workout', 'code' => 'post_workout', 'sort_order' => 6],
            ['name' => 'Dinner',       'code' => 'dinner',       'sort_order' => 7],
            ['name' => 'Bedtime',      'code' => 'bedtime',      'sort_order' => 8],
        ];

        foreach ($types as $type) {
            MealType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}

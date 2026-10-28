<?php

namespace Database\Seeders;

use App\Models\DifficultyLevel;
use Illuminate\Database\Seeder;

class DifficultyLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Beginner',     'code' => 'beginner'],
            ['name' => 'Intermediate', 'code' => 'intermediate'],
            ['name' => 'Advanced',     'code' => 'advanced'],
        ];

        foreach ($levels as $level) {
            DifficultyLevel::updateOrCreate(['code' => $level['code']], $level);
        }
    }
}

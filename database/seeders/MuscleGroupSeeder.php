<?php

namespace Database\Seeders;

use App\Models\MuscleGroup;
use Illuminate\Database\Seeder;

class MuscleGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Chest',       'code' => 'chest',       'description' => 'Pectoral muscles'],
            ['name' => 'Back',        'code' => 'back',        'description' => 'Latissimus dorsi, rhomboids, erector spinae'],
            ['name' => 'Shoulders',   'code' => 'shoulders',   'description' => 'Deltoid muscles (anterior, lateral, posterior)'],
            ['name' => 'Biceps',      'code' => 'biceps',      'description' => 'Biceps brachii and brachialis'],
            ['name' => 'Triceps',     'code' => 'triceps',     'description' => 'Triceps brachii'],
            ['name' => 'Quadriceps',  'code' => 'quadriceps',  'description' => 'Front thigh muscles'],
            ['name' => 'Hamstrings',  'code' => 'hamstrings',  'description' => 'Back thigh muscles'],
            ['name' => 'Glutes',      'code' => 'glutes',      'description' => 'Gluteus maximus, medius, minimus'],
            ['name' => 'Calves',      'code' => 'calves',      'description' => 'Gastrocnemius and soleus'],
            ['name' => 'Core',        'code' => 'core',        'description' => 'Abdominals and obliques'],
            ['name' => 'Forearms',    'code' => 'forearms',    'description' => 'Forearm flexors and extensors'],
            ['name' => 'Traps',       'code' => 'traps',       'description' => 'Trapezius muscles'],
            ['name' => 'Full Body',   'code' => 'full_body',   'description' => 'Compound movements targeting multiple muscle groups'],
        ];

        foreach ($groups as $group) {
            MuscleGroup::updateOrCreate(['code' => $group['code']], $group);
        }
    }
}

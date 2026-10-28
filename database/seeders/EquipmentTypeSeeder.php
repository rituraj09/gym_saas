<?php

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Barbell',         'code' => 'barbell',         'description' => 'Standard or Olympic barbell'],
            ['name' => 'Dumbbell',        'code' => 'dumbbell',        'description' => 'Free-weight dumbbells'],
            ['name' => 'Cable Machine',   'code' => 'cable_machine',   'description' => 'Cable pulley system'],
            ['name' => 'Smith Machine',   'code' => 'smith_machine',   'description' => 'Guided barbell machine'],
            ['name' => 'Bodyweight',      'code' => 'bodyweight',      'description' => 'No equipment needed'],
            ['name' => 'Kettlebell',      'code' => 'kettlebell',      'description' => 'Cast-iron kettlebell'],
            ['name' => 'Resistance Band', 'code' => 'resistance_band', 'description' => 'Elastic resistance bands'],
            ['name' => 'EZ Bar',          'code' => 'ez_bar',          'description' => 'Curved EZ curl bar'],
            ['name' => 'Machine',         'code' => 'machine',         'description' => 'Weight stack or plate-loaded machine'],
            ['name' => 'Pull-up Bar',     'code' => 'pull_up_bar',     'description' => 'Fixed or mounted pull-up bar'],
            ['name' => 'Bench',           'code' => 'bench',           'description' => 'Flat, incline, or decline bench'],
            ['name' => 'None',            'code' => 'none',            'description' => 'No specific equipment'],
        ];

        foreach ($types as $type) {
            EquipmentType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}

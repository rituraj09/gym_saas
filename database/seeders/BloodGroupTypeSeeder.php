<?php

namespace Database\Seeders;

use App\Models\BloodGroupType;
use Illuminate\Database\Seeder;

class BloodGroupTypeSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => 'a_positive', 'name' => 'A+'],
            ['code' => 'a_negative', 'name' => 'A-'],
            ['code' => 'b_positive', 'name' => 'B+'],
            ['code' => 'b_negative', 'name' => 'B-'],
            ['code' => 'ab_positive', 'name' => 'AB+'],
            ['code' => 'ab_negative', 'name' => 'AB-'],
            ['code' => 'o_positive', 'name' => 'O+'],
            ['code' => 'o_negative', 'name' => 'O-'],
        ];

        foreach ($groups as $group) {
            BloodGroupType::updateOrCreate(
                ['code' => $group['code']],
                ['name' => $group['name']]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\GenderType;
use Illuminate\Database\Seeder;

class GenderTypeSeeder extends Seeder
{
    public function run(): void
    {
        $genders = [
            ['code' => 'male', 'name' => 'Male'],
            ['code' => 'female', 'name' => 'Female'],
            ['code' => 'other', 'name' => 'Other'],
        ];

        foreach ($genders as $gender) {
            GenderType::updateOrCreate(
                ['code' => $gender['code']],
                ['name' => $gender['name']]
            );
        }
    }
}

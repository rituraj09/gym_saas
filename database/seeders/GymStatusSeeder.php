<?php

namespace Database\Seeders;

use App\Models\GymStatus;
use Illuminate\Database\Seeder;

class GymStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'inactive', 'name' => 'Inactive'],
            ['code' => 'suspended', 'name' => 'Suspended'],
            ['code' => 'pending', 'name' => 'Pending'],
            ['code' => 'trial', 'name' => 'Trial'],
        ];

        foreach ($statuses as $status) {
            GymStatus::updateOrCreate(
                ['code' => $status['code']],
                ['name' => $status['name']]
            );
        }
    }
}

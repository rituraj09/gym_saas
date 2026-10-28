<?php

namespace Database\Seeders;

use App\Models\AssignmentStatus;
use Illuminate\Database\Seeder;

class AssignmentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'closed', 'name' => 'Closed'],
            ['code' => 'transferred', 'name' => 'Transferred'],
        ];

        foreach ($statuses as $status) {
            AssignmentStatus::updateOrCreate(
                ['code' => $status['code']],
                ['name' => $status['name']]
            );
        }
    }
}

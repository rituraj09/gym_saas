<?php

namespace Database\Seeders;

use App\Models\WorkoutLogStatus;
use Illuminate\Database\Seeder;

class WorkoutLogStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Planned',   'code' => 'planned'],
            ['name' => 'Completed', 'code' => 'completed'],
            ['name' => 'Skipped',   'code' => 'skipped'],
        ];

        foreach ($statuses as $status) {
            WorkoutLogStatus::updateOrCreate(['code' => $status['code']], $status);
        }
    }
}

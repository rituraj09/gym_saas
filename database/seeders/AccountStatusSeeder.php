<?php

namespace Database\Seeders;

use App\Models\AccountStatus;
use Illuminate\Database\Seeder;

class AccountStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'inactive', 'name' => 'Inactive'],
            ['code' => 'suspended', 'name' => 'Suspended'],
            ['code' => 'pending', 'name' => 'Pending'],
        ];

        foreach ($statuses as $status) {
            AccountStatus::updateOrCreate(
                ['code' => $status['code']],
                ['name' => $status['name']]
            );
        }
    }
}

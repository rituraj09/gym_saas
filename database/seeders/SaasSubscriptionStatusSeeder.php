<?php

namespace Database\Seeders;

use App\Models\SaasSubscriptionStatus;
use Illuminate\Database\Seeder;

class SaasSubscriptionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Active',    'code' => 'active'],
            ['name' => 'Trial',     'code' => 'trial'],
            ['name' => 'Expired',   'code' => 'expired'],
            ['name' => 'Cancelled', 'code' => 'cancelled'],
            ['name' => 'Suspended', 'code' => 'suspended'],
        ];

        foreach ($statuses as $status) {
            SaasSubscriptionStatus::updateOrCreate(['code' => $status['code']], $status);
        }
    }
}

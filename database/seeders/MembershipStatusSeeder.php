<?php

namespace Database\Seeders;

use App\Models\MembershipStatus;
use Illuminate\Database\Seeder;

class MembershipStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'name' => 'Active'],
            ['code' => 'expiring_soon', 'name' => 'Expiring Soon'],
            ['code' => 'expired', 'name' => 'Expired'],
            ['code' => 'upcoming', 'name' => 'Upcoming'],
            ['code' => 'cancelled', 'name' => 'Cancelled'],
        ];

        foreach ($statuses as $s) {
            MembershipStatus::updateOrCreate(['code' => $s['code']], ['name' => $s['name']]);
        }
    }
}

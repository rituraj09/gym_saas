<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'completed', 'name' => 'Completed'],
            ['code' => 'pending', 'name' => 'Pending'],
            ['code' => 'failed', 'name' => 'Failed'],
            ['code' => 'refunded', 'name' => 'Refunded'],
        ];

        foreach ($statuses as $s) {
            PaymentStatus::updateOrCreate(['code' => $s['code']], ['name' => $s['name']]);
        }
    }
}

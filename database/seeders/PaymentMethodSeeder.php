<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['code' => 'cash', 'name' => 'Cash'],
            ['code' => 'card', 'name' => 'Card'],
            ['code' => 'bank_transfer', 'name' => 'Bank Transfer'],
            ['code' => 'upi', 'name' => 'UPI'],
            ['code' => 'other', 'name' => 'Other'],
        ];

        foreach ($methods as $m) {
            PaymentMethod::updateOrCreate(['code' => $m['code']], ['name' => $m['name']]);
        }
    }
}

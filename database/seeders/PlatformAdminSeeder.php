<?php

namespace Database\Seeders;

use App\Models\AccountStatus;
use App\Models\PlatformAdmin;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserRoleAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        $activeStatus = AccountStatus::where('code', 'active')->firstOrFail();
        $adminRole = UserRole::where('code', 'platform_admin')->firstOrFail();

        $existingAdmin = UserRoleAssignment::where('user_role_id', $adminRole->id)->first();

        if ($existingAdmin) {
            $this->command->info('Platform admin already exists. Skipping.');
            return;
        }

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gymsaas.com',
            'phone' => '+1234567890',
            'password' => Hash::make('Mind@2026'),
            'account_status_id' => $activeStatus->id,
            'email_verified_at' => now(),
        ]);

        UserRoleAssignment::create([
            'user_id' => $user->id,
            'user_role_id' => $adminRole->id,
        ]);

        // Generate admin code: ADM-000001
        $adminCode = 'ADM-' . str_pad(
            PlatformAdmin::withTrashed()->count() + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        PlatformAdmin::create([
            'user_id' => $user->id,
            'admin_code' => $adminCode,
        ]);

        $this->command->info('Platform admin created successfully.');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', 'Admin@1234'],
                ['Admin Code', $adminCode],
                ['Role', 'platform_admin'],
                ['Status', 'active'],
            ]
        );
    }
}

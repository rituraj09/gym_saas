<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['code' => 'platform_admin', 'name' => 'Platform Administrator'],
            ['code' => 'gym_owner', 'name' => 'Gym Owner'],
            ['code' => 'trainer', 'name' => 'Trainer'],
            ['code' => 'member', 'name' => 'Member'],
        ];

        foreach ($roles as $role) {
            UserRole::updateOrCreate(
                ['code' => $role['code']],
                ['name' => $role['name']]
            );
        }
    }
}

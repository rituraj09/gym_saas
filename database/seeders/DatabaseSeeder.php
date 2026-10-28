<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountStatusSeeder::class,
            UserRoleSeeder::class,
            GenderTypeSeeder::class,
            BloodGroupTypeSeeder::class,
            GymStatusSeeder::class,
            AssignmentStatusSeeder::class,
            MembershipStatusSeeder::class,
            PaymentMethodSeeder::class,
            PaymentStatusSeeder::class,
            // Phase 12
            MuscleGroupSeeder::class,
            EquipmentTypeSeeder::class,
            DifficultyLevelSeeder::class,
            ExerciseSeeder::class,
            // Phase 13
            WorkoutLogStatusSeeder::class,
            // Phase 14
            MealTypeSeeder::class,
            // Phase 15
            SaasSubscriptionStatusSeeder::class,
            SaasPlanSeeder::class,
        ]);
    }
}

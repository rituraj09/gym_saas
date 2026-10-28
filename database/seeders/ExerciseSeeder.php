<?php

namespace Database\Seeders;

use App\Models\DifficultyLevel;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $mg = fn($code) => MuscleGroup::where('code', $code)->value('id');
        $eq = fn($code) => EquipmentType::where('code', $code)->value('id');
        $dl = fn($code) => DifficultyLevel::where('code', $code)->value('id');

        $exercises = [
            // Chest
            ['name' => 'Barbell Bench Press',     'muscle' => 'chest', 'equipment' => 'barbell',       'difficulty' => 'intermediate', 'description' => 'The classic chest builder. Lie flat on a bench and press the barbell upward.'],
            ['name' => 'Incline Dumbbell Press',  'muscle' => 'chest', 'equipment' => 'dumbbell',      'difficulty' => 'intermediate', 'description' => 'Targets the upper chest using an incline bench and dumbbells.'],
            ['name' => 'Dumbbell Fly',            'muscle' => 'chest', 'equipment' => 'dumbbell',      'difficulty' => 'beginner',     'description' => 'Isolation movement for chest stretch and contraction.'],
            ['name' => 'Push-Up',                 'muscle' => 'chest', 'equipment' => 'bodyweight',    'difficulty' => 'beginner',     'description' => 'Fundamental bodyweight chest exercise.'],
            ['name' => 'Cable Crossover',         'muscle' => 'chest', 'equipment' => 'cable_machine', 'difficulty' => 'intermediate', 'description' => 'Constant-tension chest fly using cable pulleys.'],

            // Back
            ['name' => 'Barbell Deadlift',        'muscle' => 'back', 'equipment' => 'barbell',       'difficulty' => 'advanced',     'description' => 'Full posterior chain compound lift from the floor.'],
            ['name' => 'Pull-Up',                 'muscle' => 'back', 'equipment' => 'pull_up_bar',   'difficulty' => 'intermediate', 'description' => 'Vertical pulling movement for lats and upper back.'],
            ['name' => 'Barbell Bent-Over Row',   'muscle' => 'back', 'equipment' => 'barbell',       'difficulty' => 'intermediate', 'description' => 'Horizontal pulling movement for back thickness.'],
            ['name' => 'Lat Pulldown',            'muscle' => 'back', 'equipment' => 'cable_machine', 'difficulty' => 'beginner',     'description' => 'Machine-assisted vertical pull targeting the lats.'],
            ['name' => 'Seated Cable Row',        'muscle' => 'back', 'equipment' => 'cable_machine', 'difficulty' => 'beginner',     'description' => 'Seated horizontal pull for mid-back development.'],

            // Shoulders
            ['name' => 'Overhead Press',          'muscle' => 'shoulders', 'equipment' => 'barbell',       'difficulty' => 'intermediate', 'description' => 'Standing barbell press overhead for shoulder strength.'],
            ['name' => 'Dumbbell Lateral Raise',  'muscle' => 'shoulders', 'equipment' => 'dumbbell',      'difficulty' => 'beginner',     'description' => 'Isolation exercise for the lateral deltoid.'],
            ['name' => 'Dumbbell Front Raise',    'muscle' => 'shoulders', 'equipment' => 'dumbbell',      'difficulty' => 'beginner',     'description' => 'Targets the anterior deltoid.'],
            ['name' => 'Face Pull',               'muscle' => 'shoulders', 'equipment' => 'cable_machine', 'difficulty' => 'beginner',     'description' => 'Rear delt and rotator cuff health exercise.'],
            ['name' => 'Arnold Press',            'muscle' => 'shoulders', 'equipment' => 'dumbbell',      'difficulty' => 'intermediate', 'description' => 'Rotational shoulder press hitting all three delt heads.'],

            // Biceps
            ['name' => 'Barbell Curl',            'muscle' => 'biceps', 'equipment' => 'barbell',       'difficulty' => 'beginner',     'description' => 'Classic biceps builder with a straight bar.'],
            ['name' => 'Dumbbell Hammer Curl',    'muscle' => 'biceps', 'equipment' => 'dumbbell',      'difficulty' => 'beginner',     'description' => 'Neutral-grip curl targeting brachialis and forearms.'],
            ['name' => 'Preacher Curl',           'muscle' => 'biceps', 'equipment' => 'ez_bar',        'difficulty' => 'intermediate', 'description' => 'Strict curl on a preacher bench for peak contraction.'],
            ['name' => 'Cable Curl',              'muscle' => 'biceps', 'equipment' => 'cable_machine', 'difficulty' => 'beginner',     'description' => 'Constant-tension biceps curl using a cable pulley.'],

            // Triceps
            ['name' => 'Tricep Rope Pushdown',    'muscle' => 'triceps', 'equipment' => 'cable_machine', 'difficulty' => 'beginner',     'description' => 'Cable pushdown with rope attachment for tricep isolation.'],
            ['name' => 'Overhead Tricep Extension','muscle' => 'triceps', 'equipment' => 'dumbbell',      'difficulty' => 'beginner',     'description' => 'Overhead extension for the long head of the tricep.'],
            ['name' => 'Close-Grip Bench Press',  'muscle' => 'triceps', 'equipment' => 'barbell',       'difficulty' => 'intermediate', 'description' => 'Narrow-grip bench press emphasizing triceps.'],
            ['name' => 'Skull Crusher',           'muscle' => 'triceps', 'equipment' => 'ez_bar',        'difficulty' => 'intermediate', 'description' => 'Lying tricep extension lowering the bar to the forehead.'],
            ['name' => 'Bench Dip',               'muscle' => 'triceps', 'equipment' => 'bodyweight',    'difficulty' => 'beginner',     'description' => 'Bodyweight dip off a bench for tricep activation.'],

            // Quadriceps
            ['name' => 'Barbell Back Squat',      'muscle' => 'quadriceps', 'equipment' => 'barbell', 'difficulty' => 'intermediate', 'description' => 'The king of leg exercises. Full-depth back squat.'],
            ['name' => 'Leg Press',               'muscle' => 'quadriceps', 'equipment' => 'machine',   'difficulty' => 'beginner',     'description' => 'Machine-based leg press for quad development.'],
            ['name' => 'Walking Lunge',           'muscle' => 'quadriceps', 'equipment' => 'dumbbell',  'difficulty' => 'beginner',     'description' => 'Dynamic lunge movement for legs and balance.'],
            ['name' => 'Leg Extension',           'muscle' => 'quadriceps', 'equipment' => 'machine',   'difficulty' => 'beginner',     'description' => 'Isolation machine exercise for the quadriceps.'],

            // Hamstrings
            ['name' => 'Romanian Deadlift',       'muscle' => 'hamstrings', 'equipment' => 'barbell', 'difficulty' => 'intermediate', 'description' => 'Hip-hinge movement targeting hamstrings and glutes.'],
            ['name' => 'Lying Leg Curl',          'muscle' => 'hamstrings', 'equipment' => 'machine',   'difficulty' => 'beginner',     'description' => 'Machine isolation for hamstring contraction.'],

            // Glutes
            ['name' => 'Barbell Hip Thrust',      'muscle' => 'glutes', 'equipment' => 'barbell',    'difficulty' => 'intermediate', 'description' => 'The best glute builder. Hip thrust with barbell across hips.'],
            ['name' => 'Bulgarian Split Squat',   'muscle' => 'glutes', 'equipment' => 'dumbbell',   'difficulty' => 'intermediate', 'description' => 'Single-leg squat with rear foot elevated.'],
            ['name' => 'Glute Bridge',            'muscle' => 'glutes', 'equipment' => 'bodyweight', 'difficulty' => 'beginner',     'description' => 'Bodyweight hip bridge for glute activation.'],

            // Calves
            ['name' => 'Standing Calf Raise',     'muscle' => 'calves', 'equipment' => 'machine', 'difficulty' => 'beginner', 'description' => 'Standing calf raise on a machine or step.'],
            ['name' => 'Seated Calf Raise',       'muscle' => 'calves', 'equipment' => 'machine', 'difficulty' => 'beginner', 'description' => 'Seated calf raise targeting the soleus.'],

            // Core
            ['name' => 'Plank',                   'muscle' => 'core', 'equipment' => 'bodyweight',  'difficulty' => 'beginner',     'description' => 'Isometric core hold for stability and endurance.'],
            ['name' => 'Bicycle Crunch',          'muscle' => 'core', 'equipment' => 'bodyweight',  'difficulty' => 'beginner',     'description' => 'Rotational crunch targeting obliques and rectus abdominis.'],
            ['name' => 'Hanging Leg Raise',       'muscle' => 'core', 'equipment' => 'pull_up_bar', 'difficulty' => 'advanced',     'description' => 'Advanced core exercise hanging from a bar.'],
            ['name' => 'Russian Twist',           'muscle' => 'core', 'equipment' => 'bodyweight',  'difficulty' => 'intermediate', 'description' => 'Seated rotational movement for obliques.'],
            ['name' => 'Ab Wheel Rollout',        'muscle' => 'core', 'equipment' => 'bodyweight',  'difficulty' => 'advanced',     'description' => 'Kneeling rollout using an ab wheel for deep core strength.'],

            // Traps
            ['name' => 'Barbell Shrug',           'muscle' => 'traps', 'equipment' => 'barbell',  'difficulty' => 'beginner', 'description' => 'Simple shrug movement for upper trap development.'],
            ['name' => 'Dumbbell Shrug',          'muscle' => 'traps', 'equipment' => 'dumbbell', 'difficulty' => 'beginner', 'description' => 'Dumbbell shrug allowing greater range of motion.'],

            // Full Body
            ['name' => 'Burpee',                  'muscle' => 'full_body', 'equipment' => 'bodyweight', 'difficulty' => 'intermediate', 'description' => 'Full-body explosive movement combining squat, push-up, and jump.'],
            ['name' => 'Kettlebell Swing',        'muscle' => 'full_body', 'equipment' => 'kettlebell', 'difficulty' => 'intermediate', 'description' => 'Hip-hinge explosive swing for posterior chain and conditioning.'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::updateOrCreate(
                ['slug' => Str::slug($exercise['name'])],
                [
                    'name'              => $exercise['name'],
                    'description'       => $exercise['description'],
                    'muscle_group_id'   => $mg($exercise['muscle']),
                    'equipment_type_id' => $eq($exercise['equipment']),
                    'difficulty_level_id' => $dl($exercise['difficulty']),
                    'is_active'         => true,
                ]
            );
        }
    }
}

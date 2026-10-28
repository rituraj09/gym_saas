<?php

namespace App\Services;

use App\Models\MemberExercisePlan;
use App\Models\MemberWorkoutLog;
use App\Models\WorkoutLogStatus;
use App\Models\Gym;
use Illuminate\Support\Facades\DB;

class MemberExercisePlanService
{
    public function create(Gym $gym, int $userId, array $data): MemberExercisePlan
    {
        return DB::transaction(function () use ($gym, $userId, $data) {
            $plan = MemberExercisePlan::create([
                'gym_id'             => $gym->id,
                'member_id'          => $data['member_id'],
                'created_by_user_id' => $userId,
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'start_date'         => $data['start_date'],
                'end_date'           => $data['end_date'] ?? null,
                'is_active'          => true,
            ]);

            $this->syncWorkoutDays($plan, $data['days']);

            return $plan->load(['workoutDays.exerciseTemplate', 'member']);
        });
    }

    public function update(MemberExercisePlan $plan, array $data): MemberExercisePlan
    {
        return DB::transaction(function () use ($plan, $data) {
            $plan->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date'  => $data['start_date'],
                'end_date'    => $data['end_date'] ?? null,
            ]);

            $plan->workoutDays()->forceDelete();
            $this->syncWorkoutDays($plan, $data['days']);

            return $plan->load(['workoutDays.exerciseTemplate', 'member']);
        });
    }

    public function toggleStatus(MemberExercisePlan $plan): MemberExercisePlan
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return $plan;
    }

    public function logWorkout(MemberExercisePlan $plan, int $workoutDayId, string $date, string $statusCode, ?string $notes = null): MemberWorkoutLog
    {
        $statusId = WorkoutLogStatus::where('code', $statusCode)->value('id');

        return MemberWorkoutLog::updateOrCreate(
            [
                'member_exercise_plan_id' => $plan->id,
                'member_workout_day_id'   => $workoutDayId,
                'workout_date'            => $date,
            ],
            [
                'workout_log_status_id' => $statusId,
                'notes'                 => $notes,
            ]
        );
    }

    protected function syncWorkoutDays(MemberExercisePlan $plan, array $days): void
    {
        foreach ($days as $index => $day) {
            $plan->workoutDays()->create([
                'day_of_week'        => $day['day_of_week'],
                'exercise_template_id' => $day['exercise_template_id'],
                'notes'              => $day['notes'] ?? null,
                'sort_order'         => $index,
            ]);
        }
    }
}

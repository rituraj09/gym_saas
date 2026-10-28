<?php

namespace App\Services;

use App\Models\ExerciseTemplate;
use App\Models\Gym;
use Illuminate\Support\Facades\DB;

class ExerciseTemplateService
{
    public function create(Gym $gym, int $userId, array $data): ExerciseTemplate
    {
        return DB::transaction(function () use ($gym, $userId, $data) {
            $template = ExerciseTemplate::create([
                'gym_id'                   => $gym->id,
                'created_by_user_id'       => $userId,
                'name'                     => $data['name'],
                'description'              => $data['description'] ?? null,
                'target_muscle_group_id'   => $data['target_muscle_group_id'] ?? null,
                'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? null,
                'is_active'                => true,
            ]);

            $this->syncItems($template, $data['items']);

            return $template->load('items.exercise.muscleGroup');
        });
    }

    public function update(ExerciseTemplate $template, array $data): ExerciseTemplate
    {
        return DB::transaction(function () use ($template, $data) {
            $template->update([
                'name'                     => $data['name'],
                'description'              => $data['description'] ?? null,
                'target_muscle_group_id'   => $data['target_muscle_group_id'] ?? null,
                'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? null,
            ]);

            // Delete existing items and recreate
            $template->items()->forceDelete();
            $this->syncItems($template, $data['items']);

            return $template->load('items.exercise.muscleGroup');
        });
    }

    public function toggleStatus(ExerciseTemplate $template): ExerciseTemplate
    {
        $template->update(['is_active' => !$template->is_active]);
        return $template;
    }

    protected function syncItems(ExerciseTemplate $template, array $items): void
    {
        foreach ($items as $index => $item) {
            $template->items()->create([
                'exercise_id'  => $item['exercise_id'],
                'sets'         => $item['sets'],
                'reps'         => $item['reps'],
                'rest_seconds' => $item['rest_seconds'] ?? 60,
                'weight_kg'    => $item['weight_kg'] ?: null,
                'notes'        => $item['notes'] ?? null,
                'sort_order'   => $index,
            ]);
        }
    }
}

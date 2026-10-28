<?php

namespace App\Services;

use App\Models\DietPlanTemplate;
use App\Models\MemberDietPlan;
use App\Models\MemberDietLog;
use App\Models\Gym;
use Illuminate\Support\Facades\DB;

class DietPlanService
{
    public function createTemplate(Gym $gym, int $userId, array $data): DietPlanTemplate
    {
        return DB::transaction(function () use ($gym, $userId, $data) {
            $template = DietPlanTemplate::create([
                'gym_id'             => $gym->id,
                'created_by_user_id' => $userId,
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'target_goal'        => $data['target_goal'] ?? null,
                'total_calories'     => $data['total_calories'] ?? null,
                'total_protein_g'    => $data['total_protein_g'] ?? null,
                'total_carbs_g'      => $data['total_carbs_g'] ?? null,
                'total_fat_g'        => $data['total_fat_g'] ?? null,
                'is_active'          => true,
            ]);

            $this->syncMeals($template, $data['meals']);

            return $template->load('meals.items');
        });
    }

    public function updateTemplate(DietPlanTemplate $template, array $data): DietPlanTemplate
    {
        return DB::transaction(function () use ($template, $data) {
            $template->update([
                'name'            => $data['name'],
                'description'     => $data['description'] ?? null,
                'target_goal'     => $data['target_goal'] ?? null,
                'total_calories'  => $data['total_calories'] ?? null,
                'total_protein_g' => $data['total_protein_g'] ?? null,
                'total_carbs_g'   => $data['total_carbs_g'] ?? null,
                'total_fat_g'     => $data['total_fat_g'] ?? null,
            ]);

            $template->meals()->each(function ($meal) {
                $meal->items()->forceDelete();
            });
            $template->meals()->forceDelete();

            $this->syncMeals($template, $data['meals']);

            return $template->load('meals.items');
        });
    }

    public function toggleTemplateStatus(DietPlanTemplate $template): DietPlanTemplate
    {
        $template->update(['is_active' => !$template->is_active]);
        return $template;
    }

    public function assignToMember(Gym $gym, int $userId, array $data): MemberDietPlan
    {
        return MemberDietPlan::create([
            'gym_id'                => $gym->id,
            'member_id'             => $data['member_id'],
            'diet_plan_template_id' => $data['diet_plan_template_id'],
            'created_by_user_id'    => $userId,
            'start_date'            => $data['start_date'],
            'end_date'              => $data['end_date'] ?? null,
            'notes'                 => $data['notes'] ?? null,
            'is_active'             => true,
        ]);
    }

    public function logDiet(MemberDietPlan $plan, string $date, string $status, ?string $notes = null): MemberDietLog
    {
        return MemberDietLog::updateOrCreate(
            [
                'member_diet_plan_id' => $plan->id,
                'log_date'            => $date,
            ],
            [
                'status' => $status,
                'notes'  => $notes,
            ]
        );
    }

    protected function syncMeals(DietPlanTemplate $template, array $meals): void
    {
        foreach ($meals as $mealIndex => $mealData) {
            $meal = $template->meals()->create([
                'meal_type_id' => $mealData['meal_type_id'],
                'meal_time'    => $mealData['meal_time'] ?? null,
                'notes'        => $mealData['notes'] ?? null,
                'sort_order'   => $mealIndex,
            ]);

            foreach ($mealData['items'] as $itemIndex => $itemData) {
                $meal->items()->create([
                    'food_name'  => $itemData['food_name'],
                    'quantity'   => $itemData['quantity'],
                    'calories'   => $itemData['calories'],
                    'protein_g'  => $itemData['protein_g'],
                    'carbs_g'    => $itemData['carbs_g'],
                    'fat_g'      => $itemData['fat_g'],
                    'notes'      => $itemData['notes'] ?? null,
                    'sort_order' => $itemIndex,
                ]);
            }
        }
    }
}

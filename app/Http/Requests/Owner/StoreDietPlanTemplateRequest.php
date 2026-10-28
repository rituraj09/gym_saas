<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreDietPlanTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'target_goal'     => 'nullable|string|max:100',
            'total_calories'  => 'nullable|integer|min:0|max:10000',
            'total_protein_g' => 'nullable|numeric|min:0|max:9999',
            'total_carbs_g'   => 'nullable|numeric|min:0|max:9999',
            'total_fat_g'     => 'nullable|numeric|min:0|max:9999',
            'meals'           => 'required|array|min:1',
            'meals.*.meal_type_id' => 'required|exists:meal_types,id',
            'meals.*.meal_time'    => 'nullable|date_format:H:i',
            'meals.*.notes'        => 'nullable|string|max:255',
            'meals.*.items'        => 'required|array|min:1',
            'meals.*.items.*.food_name' => 'required|string|max:255',
            'meals.*.items.*.quantity'  => 'required|string|max:100',
            'meals.*.items.*.calories'  => 'required|integer|min:0|max:5000',
            'meals.*.items.*.protein_g' => 'required|numeric|min:0|max:999',
            'meals.*.items.*.carbs_g'   => 'required|numeric|min:0|max:999',
            'meals.*.items.*.fat_g'     => 'required|numeric|min:0|max:999',
            'meals.*.items.*.notes'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'meals.required' => 'Add at least one meal to the diet plan.',
            'meals.*.items.required' => 'Each meal must have at least one food item.',
        ];
    }
}

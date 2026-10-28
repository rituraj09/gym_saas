<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => 'required|string|max:255',
            'description'                => 'nullable|string|max:1000',
            'target_muscle_group_id'     => 'nullable|exists:muscle_groups,id',
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:300',
            'items'                      => 'required|array|min:1',
            'items.*.exercise_id'        => 'required|exists:exercises,id',
            'items.*.sets'               => 'required|integer|min:1|max:50',
            'items.*.reps'               => 'required|integer|min:1|max:100',
            'items.*.rest_seconds'       => 'required|integer|min:0|max:600',
            'items.*.weight_kg'          => 'nullable|numeric|min:0|max:999.9',
            'items.*.notes'              => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'         => 'Add at least one exercise to the template.',
            'items.min'              => 'Add at least one exercise to the template.',
            'items.*.exercise_id.required' => 'Select an exercise for each item.',
            'items.*.sets.required'  => 'Sets are required for each exercise.',
            'items.*.reps.required'  => 'Reps are required for each exercise.',
        ];
    }
}

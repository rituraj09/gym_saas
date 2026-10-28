<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberExercisePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'days'         => 'required|array|min:1',
            'days.*.day_of_week'        => 'required|integer|min:1|max:7',
            'days.*.exercise_template_id' => 'required|exists:exercise_templates,id',
            'days.*.notes'              => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'days.required' => 'Add at least one workout day.',
            'days.min'      => 'Add at least one workout day.',
        ];
    }
}

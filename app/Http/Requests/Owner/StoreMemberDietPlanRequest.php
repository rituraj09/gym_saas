<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberDietPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id'             => 'required|exists:members,id',
            'diet_plan_template_id' => 'required|exists:diet_plan_templates,id',
            'start_date'            => 'required|date',
            'end_date'              => 'nullable|date|after_or_equal:start_date',
            'notes'                 => 'nullable|string|max:1000',
        ];
    }
}

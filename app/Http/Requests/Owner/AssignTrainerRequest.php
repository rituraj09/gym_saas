<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class AssignTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id'  => ['required', 'exists:members,id'],
            'trainer_id' => ['required', 'exists:trainers,id'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required'  => 'Please select a member.',
            'trainer_id.required' => 'Please select a trainer.',
            'start_date.required' => 'Start date is required.',
        ];
    }
}

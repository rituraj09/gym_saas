<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'member_id'          => ['required', 'exists:members,id'],
            'membership_plan_id' => ['required', 'exists:membership_plans,id'],
            'start_date'         => ['required', 'date'],
            'notes'              => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required'          => 'Please select a member.',
            'membership_plan_id.required' => 'Please select a plan.',
            'start_date.required'         => 'Start date is required.',
        ];
    }
}

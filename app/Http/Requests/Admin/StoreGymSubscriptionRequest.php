<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGymSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id'        => 'required|exists:gyms,id',
            'saas_plan_id'  => 'required|exists:saas_plans,id',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'trial_days'    => 'nullable|integer|min:0|max:90',
            'notes'         => 'nullable|string|max:1000',
        ];
    }
}

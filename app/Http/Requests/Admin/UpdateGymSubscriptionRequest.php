<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGymSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'saas_plan_id' => ['required', 'exists:saas_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'saas_subscription_status_id' => ['required', 'exists:saas_subscription_statuses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}

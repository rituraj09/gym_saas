<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaasPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_subscription_id' => ['required', 'exists:gym_subscriptions,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payment_status_id' => ['required', 'exists:payment_statuses,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'payment_date' => ['required', 'date'],
            'billing_period_start' => ['required', 'date'],
            'billing_period_end' => ['required', 'date', 'after_or_equal:billing_period_start'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'billing_period_end.after_or_equal' => 'The billing period end must be on or after the start date.',
            'amount.min' => 'The payment amount must be at least $0.01.',
        ];
    }
}

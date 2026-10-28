<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'member_id'           => ['required', 'exists:members,id'],
            'membership_id'       => ['nullable', 'exists:memberships,id'],
            'payment_method_id'   => ['required', 'exists:payment_methods,id'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'payment_date'        => ['required', 'date', 'before_or_equal:today'],
            'notes'               => ['nullable', 'string', 'max:500'],
            'receipt'             => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required'         => 'Please select a member.',
            'payment_method_id.required' => 'Please select a payment method.',
            'amount.required'            => 'Amount is required.',
            'amount.min'                 => 'Amount must be greater than zero.',
            'payment_date.required'      => 'Payment date is required.',
        ];
    }
}

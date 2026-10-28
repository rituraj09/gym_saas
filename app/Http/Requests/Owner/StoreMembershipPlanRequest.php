<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipPlanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:150'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'price'         => ['required', 'numeric', 'min:0'],
            'description'   => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Plan name is required.',
            'duration_days.required' => 'Duration is required.',
            'duration_days.min'      => 'Duration must be at least 1 day.',
            'price.required'         => 'Price is required.',
            'price.min'              => 'Price cannot be negative.',
        ];
    }
}

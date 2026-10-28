<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipPlanRequest extends FormRequest
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
}

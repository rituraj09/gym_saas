<?php

namespace App\Http\Requests\Admin;

use App\Models\GymModuleAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGymModuleAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_id' => ['required', 'exists:gyms,id'],
            'module_code' => [
                'required',
                Rule::in(array_keys(GymModuleAccess::MODULES)),
                Rule::unique('gym_module_access')
                    ->where('gym_id', $this->gym_id)
                    ->whereNull('deleted_at'),
            ],
            'is_enabled' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'module_code.unique' => 'An override for this module already exists for this gym.',
            'expires_at.after' => 'The expiry date must be in the future.',
        ];
    }
}

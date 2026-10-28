<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGymProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:150'],
            'email'        => ['nullable', 'email', 'max:150'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'address'      => ['required', 'string', 'max:500'],
            'city'         => ['required', 'string', 'max:100'],
            'state'        => ['required', 'string', 'max:100'],
            'zip_code'     => ['nullable', 'string', 'max:20'],
            'logo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Gym name is required.',
            'address.required' => 'Address is required.',
            'city.required'    => 'City is required.',
            'state.required'   => 'State is required.',
            'logo.image'       => 'Logo must be an image file.',
            'logo.max'         => 'Logo must not exceed 2MB.',
        ];
    }
}

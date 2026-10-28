<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class GymRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Gym details
            'gym_name'         => ['required', 'string', 'max:150'],
            'gym_phone'        => ['nullable', 'string', 'max:20'],
            'gym_email'        => ['nullable', 'email', 'max:150'],
            'gym_address'      => ['required', 'string', 'max:500'],
            'gym_city'         => ['required', 'string', 'max:100'],
            'gym_state'        => ['required', 'string', 'max:100'],
            'gym_zip_code'     => ['nullable', 'string', 'max:20'],
            'gym_logo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Owner details
            'owner_name'       => ['required', 'string', 'max:150'],
            'owner_email'      => ['required', 'email', 'max:150'],
            'owner_phone'      => ['required', 'string', 'max:20'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            // Legal
            'terms'            => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'gym_name.required'    => 'Please enter your gym name.',
            'gym_address.required' => 'Gym address is required.',
            'gym_city.required'    => 'City is required.',
            'gym_state.required'   => 'State is required.',
            'owner_name.required'  => 'Please enter the owner\'s full name.',
            'owner_email.required' => 'Owner email is required for login.',
            'owner_email.email'    => 'Please enter a valid email address.',
            'owner_phone.required' => 'Owner phone number is required.',
            'password.required'    => 'Please set a password.',
            'password.confirmed'   => 'Password confirmation does not match.',
            'terms.accepted'       => 'You must accept the terms and conditions.',
            'gym_logo.image'       => 'The gym logo must be an image file.',
            'gym_logo.max'         => 'The gym logo must not be larger than 2MB.',
        ];
    }
}

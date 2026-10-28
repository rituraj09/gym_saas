<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateOwnerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:150'],
            'email'                 => ['required', 'email', 'max:150'],
            'phone'                 => ['required', 'string', 'max:20'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'address'               => ['nullable', 'string', 'max:500'],
            'profile_photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'              => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Full name is required.',
            'email.required'     => 'Email is required.',
            'phone.required'     => 'Phone number is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}

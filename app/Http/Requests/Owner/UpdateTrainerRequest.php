<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:150'],
            'email'            => ['required', 'email', 'max:150'],
            'phone'            => ['required', 'string', 'max:20'],
            'password'         => ['nullable', 'confirmed', Password::min(8)],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'gender_id'        => ['nullable', 'exists:gender_types,id'],
            'blood_group_id'   => ['nullable', 'exists:blood_group_types,id'],
            'address'          => ['nullable', 'string', 'max:500'],
            'specialization'   => ['nullable', 'string', 'max:100'],
            'qualification'    => ['nullable', 'string', 'max:100'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'joining_date'     => ['required', 'date', 'before_or_equal:today'],
            'profile_photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}

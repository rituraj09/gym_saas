<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Check account status after successful credential match
        $user = Auth::user();

        if (! $user->isActive()) {
            Auth::logout();

            $this->session()->invalidate();
            $this->session()->regenerateToken();

            RateLimiter::hit($this->throttleKey());

            $statusMessage = match ($user->accountStatus?->code) {
                'suspended' => 'Your account has been suspended. Please contact support.',
                'pending'   => 'Your account is pending approval. Please wait for activation.',
                'inactive'  => 'Your account is inactive. Please contact support.',
                default     => 'Your account is not active. Please contact support.',
            };

            throw ValidationException::withMessages([
                'email' => $statusMessage,
            ]);
        }

        // Check that user has at least one role assigned
        if (! $user->getPrimaryRole()) {
            Auth::logout();

            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'No role has been assigned to your account. Please contact support.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}

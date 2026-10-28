<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GymRegistrationRequest;
use App\Services\GymRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GymRegistrationController extends Controller
{
    public function __construct(
        protected GymRegistrationService $registrationService
    ) {}

    public function create(): View
    {
        return view('auth.register-gym');
    }

    public function store(GymRegistrationRequest $request): RedirectResponse
    {
        try {
            $result = $this->registrationService->register(
                $request->validated(),
                $request->file('gym_logo')
            );

            // Auto-login the new owner
            Auth::login($result['user']);
            $request->session()->regenerate();

            return redirect()
                ->route('owner.dashboard')
                ->with('success', "Welcome to GymManager! Your gym '{$result['gym']->name}' has been created successfully. You are on a free trial.");

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'registration' => 'Something went wrong while creating your gym. Please try again.',
                ]);
        }
    }
}

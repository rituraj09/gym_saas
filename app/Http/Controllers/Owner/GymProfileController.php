<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UpdateGymProfileRequest;
use App\Services\GymService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GymProfileController extends Controller
{
    public function __construct(
        protected GymService $gymService
    ) {}

    public function edit(): View
    {
        $gym = auth()->user()->gymOwner->gym;

        return view('owner.profile.gym', compact('gym'));
    }

    public function update(UpdateGymProfileRequest $request): RedirectResponse
    {
        $gym = auth()->user()->gymOwner->gym;

        $this->gymService->updateGymProfile(
            $gym,
            $request->validated(),
            $request->file('logo')
        );

        return back()->with('success', 'Gym profile updated successfully.');
    }
}

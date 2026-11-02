<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UpdateOwnerProfileRequest;
use App\Services\GymService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function __construct(
        protected GymService $gymService
    ) {}

    public function edit(): View
    {
        $user = auth()->user();

        return view('admin.settings.profile', compact('user'));
    }

    public function update(UpdateOwnerProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $owner = $user->gymOwner;

        $this->gymService->updateOwnerProfile(
            $user,
            $owner,
            $request->validated(),
            $request->file('profile_photo')
        );

        return back()->with('success', 'Your profile has been updated.');
    }
}

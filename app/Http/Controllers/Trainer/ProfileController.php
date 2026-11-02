<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainer\UpdateTrainerPasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $trainer = $user->trainer;
        $trainer->load(['gender', 'bloodGroup', 'gym']);

        return view('trainer.profile.edit', compact('user', 'trainer'));
    }

    public function updatePassword(UpdateTrainerPasswordRequest $request)
    {
        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}

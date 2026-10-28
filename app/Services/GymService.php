<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymOwner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GymService
{
    public function updateGymProfile(Gym $gym, array $data, ?UploadedFile $logo = null): Gym
    {
        if ($logo) {
            // Delete old logo if exists
            if ($gym->logo) {
                Storage::disk('public')->delete($gym->logo);
            }
            $data['logo'] = $logo->store('gym-logos', 'public');
        }

        $gym->update($data);

        return $gym->fresh();
    }

    public function updateOwnerProfile(User $user, GymOwner $owner, array $data, ?UploadedFile $photo = null): void
    {
        // Update user fields
        $userData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        if (! empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        // Update owner profile fields
        $ownerData = [
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address'       => $data['address'] ?? null,
        ];

        if ($photo) {
            if ($owner->profile_photo) {
                Storage::disk('public')->delete($owner->profile_photo);
            }
            $ownerData['profile_photo'] = $photo->store('owner-photos', 'public');
        }

        $owner->update($ownerData);
    }
}

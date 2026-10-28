<?php

namespace App\Services;

use App\Models\AccountStatus;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TrainerService
{
    public function createTrainer(Gym $gym, array $data, ?UploadedFile $photo = null): Trainer
    {
        return DB::transaction(function () use ($gym, $data, $photo) {

            $activeStatusId = AccountStatus::where('code', 'active')->value('id');
            $trainerRoleId = UserRole::where('code', 'trainer')->value('id');

            $photoPath = $photo ? $photo->store('trainer-photos', 'public') : null;

            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'password'          => Hash::make($data['password']),
                'account_status_id' => $activeStatusId,
                'email_verified_at' => now(),
            ]);

            $user->roles()->attach($trainerRoleId);

                       $trainer = Trainer::create([
                'user_id'          => $user->id,
                'gym_id'           => $gym->id,
                'trainer_code'     => $this->generateCode($gym),
                'date_of_birth'    => $data['date_of_birth'] ?? null,
                'gender_id'        => $data['gender_id'] ?? null,
                'blood_group_id'   => $data['blood_group_id'] ?? null,
                'address'          => $data['address'] ?? null,
                'specialization'   => $data['specialization'] ?? null,
                'qualification'    => $data['qualification'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'joining_date'     => $data['joining_date'],
                'profile_photo'    => $photoPath,
            ]);

            return $trainer;
        });
    }

    public function updateTrainer(Trainer $trainer, array $data, ?UploadedFile $photo = null): Trainer
    {
        return DB::transaction(function () use ($trainer, $data, $photo) {

            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $trainer->user->update($userData);

            $trainerData = [
                'date_of_birth'    => $data['date_of_birth'] ?? null,
                'gender_id'        => $data['gender_id'] ?? null,
                'blood_group_id'   => $data['blood_group_id'] ?? null,
                'address'          => $data['address'] ?? null,
                'specialization'   => $data['specialization'] ?? null,
                'qualification'    => $data['qualification'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'joining_date'     => $data['joining_date'],
            ];

            if ($photo) {
                if ($trainer->profile_photo) {
                    Storage::disk('public')->delete($trainer->profile_photo);
                }
                $trainerData['profile_photo'] = $photo->store('trainer-photos', 'public');
            }

            $trainer->update($trainerData);

            return $trainer->fresh(['user']);
        });
    }

    public function toggleStatus(Trainer $trainer): string
    {
        $user = $trainer->user;
        $activeId = AccountStatus::where('code', 'active')->value('id');
        $inactiveId = AccountStatus::where('code', 'inactive')->value('id');

        if ($user->account_status_id == $activeId) {
            $user->update(['account_status_id' => $inactiveId]);
            return 'deactivated';
        }

        $user->update(['account_status_id' => $activeId]);
        return 'activated';
    }

    protected function generateCode(Gym $gym): string
    {
        $count = Trainer::withTrashed()->where('gym_id', $gym->id)->count() + 1;

        return $gym->code . '-TR' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}

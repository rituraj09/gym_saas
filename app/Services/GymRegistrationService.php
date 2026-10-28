<?php

namespace App\Services;

use App\Models\AccountStatus;
use App\Models\Gym;
use App\Models\GymOwner;
use App\Models\GymStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GymRegistrationService
{
    /**
     * Register a new gym with its owner atomically.
     *
     * @param  array  $data  Validated input data
     * @param  UploadedFile|null  $logo
     * @return array{user: User, gym: Gym, owner: GymOwner}
     */
    public function register(array $data, ?UploadedFile $logo = null): array
    {
        return DB::transaction(function () use ($data, $logo) {

            // 1. Resolve lookup IDs
            $activeStatusId = AccountStatus::where('code', 'active')->value('id');
            $trialGymStatusId = GymStatus::where('code', 'trial')->value('id');
            $ownerRoleId = UserRole::where('code', 'gym_owner')->value('id');

            if (! $activeStatusId || ! $trialGymStatusId || ! $ownerRoleId) {
                throw new \RuntimeException('Required lookup records are missing. Please run seeders.');
            }

            // 2. Create User (owner login account)
            $user = User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'phone'             => $data['owner_phone'],
                'password'          => Hash::make($data['password']),
                'account_status_id' => $activeStatusId,
                'email_verified_at' => now(), // auto-verified for owner self-registration
            ]);

            // 3. Create Gym (handle optional logo upload)
            $logoPath = null;
            if ($logo) {
                $logoPath = $logo->store('gym-logos', 'public');
            }

            $gym = Gym::create([
                'name'             => $data['gym_name'],
                'code'             => $this->generateGymCode(),
                'email'            => $data['gym_email'] ?? null,
                'phone'            => $data['gym_phone'] ?? null,
                'address'          => $data['gym_address'],
                'city'             => $data['gym_city'],
                'state'            => $data['gym_state'],
                'zip_code'         => $data['gym_zip_code'] ?? null,
                'logo'             => $logoPath,
                'gym_status_id'    => $trialGymStatusId,
                'established_date' => now()->toDateString(),
            ]);

            // 4. Create Gym Owner profile
            $owner = GymOwner::create([
                'user_id'       => $user->id,
                'gym_id'        => $gym->id,
                'owner_code'    => $this->generateOwnerCode($gym),
                'joining_date'  => now()->toDateString(),
            ]);

            // 5. Assign gym_owner role
            $user->roles()->attach($ownerRoleId);

            return [
                'user'  => $user,
                'gym'   => $gym,
                'owner' => $owner,
            ];
        });
    }

    /**
     * Generate a unique gym code: GYM-XXXXXX
     */
    protected function generateGymCode(): string
    {
        do {
            $code = 'GYM-' . strtoupper(Str::random(6));
        } while (Gym::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    /**
     * Generate a unique owner code scoped to the gym: {GYM_CODE}-OWN001
     */
    protected function generateOwnerCode(Gym $gym): string
    {
        $count = GymOwner::withTrashed()->where('gym_id', $gym->id)->count() + 1;

        return $gym->code . '-OWN' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}

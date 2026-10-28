<?php

namespace App\Services;

use App\Models\AccountStatus;
use App\Models\Gym;
use App\Models\Member;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberService
{
    public function createMember(Gym $gym, array $data, ?UploadedFile $photo = null): Member
    {
        return DB::transaction(function () use ($gym, $data, $photo) {

            $createLogin = ! empty($data['create_login']);
            $statusCode = $createLogin ? 'active' : 'inactive';
            $statusId = AccountStatus::where('code', $statusCode)->value('id');
            $memberRoleId = UserRole::where('code', 'member')->value('id');

            $password = $createLogin && ! empty($data['password'])
                ? $data['password']
                : Str::random(16);

            $photoPath = $photo ? $photo->store('member-photos', 'public') : null;

            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'password'          => Hash::make($password),
                'account_status_id' => $statusId,
                'email_verified_at' => $createLogin ? now() : null,
            ]);

            $user->roles()->attach($memberRoleId);

            $member = Member::create([
                'user_id'                 => $user->id,
                'gym_id'                  => $gym->id,
                'member_code'             => $this->generateCode($gym),
                'date_of_birth'           => $data['date_of_birth'] ?? null,
                'gender_id'               => $data['gender_id'] ?? null,
                'blood_group_id'          => $data['blood_group_id'] ?? null,
                'address'                 => $data['address'] ?? null,
                'emergency_contact_name'  => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'joining_date'            => $data['joining_date'],
                'profile_photo'           => $photoPath,
            ]);

            return $member;
        });
    }

    public function updateMember(Member $member, array $data, ?UploadedFile $photo = null): Member
    {
        return DB::transaction(function () use ($member, $data, $photo) {

            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $member->user->update($userData);

            $memberData = [
                'date_of_birth'           => $data['date_of_birth'] ?? null,
                'gender_id'               => $data['gender_id'] ?? null,
                'blood_group_id'          => $data['blood_group_id'] ?? null,
                'address'                 => $data['address'] ?? null,
                'emergency_contact_name'  => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'joining_date'            => $data['joining_date'],
            ];

            if ($photo) {
                if ($member->profile_photo) {
                    Storage::disk('public')->delete($member->profile_photo);
                }
                $memberData['profile_photo'] = $photo->store('member-photos', 'public');
            }

            $member->update($memberData);

            return $member->fresh(['user', 'gender', 'bloodGroup']);
        });
    }

    public function toggleStatus(Member $member): string
    {
        $user = $member->user;
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
        $count = Member::withTrashed()->where('gym_id', $gym->id)->count() + 1;

        return $gym->code . '-M' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}

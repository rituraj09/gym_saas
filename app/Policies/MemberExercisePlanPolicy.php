<?php

namespace App\Policies;

use App\Models\MemberExercisePlan;
use App\Models\User;

class MemberExercisePlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['gym_owner', 'trainer']);
    }

    public function view(User $user, MemberExercisePlan $plan): bool
    {
        if ($user->hasRole('gym_owner')) {
            return $plan->gym_id === $user->gymOwner->gym_id;
        }

        if ($user->hasRole('trainer')) {
            return $plan->gym_id === $user->trainer->gym_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['gym_owner', 'trainer']);
    }

    public function update(User $user, MemberExercisePlan $plan): bool
    {
        return $this->view($user, $plan);
    }

    public function delete(User $user, MemberExercisePlan $plan): bool
    {
        return $this->view($user, $plan);
    }
}

<?php

namespace App\Policies;

use App\Models\ExerciseTemplate;
use App\Models\User;

class ExerciseTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['gym_owner', 'trainer']);
    }

    public function view(User $user, ExerciseTemplate $template): bool
    {
        if ($user->hasRole('gym_owner')) {
            return $template->gym_id === $user->gymOwner->gym_id;
        }

        if ($user->hasRole('trainer')) {
            return $template->gym_id === $user->trainer->gym_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['gym_owner', 'trainer']);
    }

    public function update(User $user, ExerciseTemplate $template): bool
    {
        if ($user->hasRole('gym_owner')) {
            return $template->gym_id === $user->gymOwner->gym_id;
        }

        if ($user->hasRole('trainer')) {
            return $template->gym_id === $user->trainer->gym_id;
        }

        return false;
    }

    public function delete(User $user, ExerciseTemplate $template): bool
    {
        return $this->update($user, $template);
    }
}

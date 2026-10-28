<?php

namespace App\Policies;

use App\Models\DietPlanTemplate;
use App\Models\User;

class DietPlanTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['gym_owner', 'trainer']);
    }

    public function view(User $user, DietPlanTemplate $template): bool
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

    public function update(User $user, DietPlanTemplate $template): bool
    {
        return $this->view($user, $template);
    }

    public function delete(User $user, DietPlanTemplate $template): bool
    {
        return $this->view($user, $template);
    }
}

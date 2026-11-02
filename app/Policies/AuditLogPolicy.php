<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Platform admins can view all audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('platform_admin') || $user->hasRole('gym_owner');
    }

    /**
     * Platform admins can view any log.
     * Gym owners can only view logs for their own gym.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('gym_owner')) {
            $owner = $user->gymOwner;
            return $owner && $auditLog->gym_id === $owner->gym_id;
        }

        return false;
    }
}

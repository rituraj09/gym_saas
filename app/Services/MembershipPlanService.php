<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\MembershipPlan;

class MembershipPlanService
{
    public function create(Gym $gym, array $data): MembershipPlan
    {
        return MembershipPlan::create(array_merge($data, [
            'gym_id' => $gym->id,
            'is_active' => true,
        ]));
    }

    public function update(MembershipPlan $plan, array $data): MembershipPlan
    {
        $plan->update($data);
        return $plan->fresh();
    }

    public function toggleStatus(MembershipPlan $plan): bool
    {
        $plan->update(['is_active' => ! $plan->is_active]);
        return $plan->is_active;
    }
}

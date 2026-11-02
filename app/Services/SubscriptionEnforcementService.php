<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SaasSubscriptionStatus;

class SubscriptionEnforcementService
{
    public function getActiveSubscription(Gym $gym): ?GymSubscription
    {
        return $gym->activeSubscription;
    }

    public function hasActiveSubscription(Gym $gym): bool
    {
        $sub = $this->getActiveSubscription($gym);
        return $sub !== null && $sub->isActive();
    }

    public function hasFeature(Gym $gym, string $feature): bool
    {
        $sub = $this->getActiveSubscription($gym);
        return $sub !== null && $sub->hasFeature($feature);
    }

    public function canAddTrainer(Gym $gym): bool
    {
        $sub = $this->getActiveSubscription($gym);
        if (!$sub) return false;
        return $sub->canAddTrainer();
    }

    public function canAddMember(Gym $gym): bool
    {
        $sub = $this->getActiveSubscription($gym);
        if (!$sub) return false;
        return $sub->canAddMember();
    }

    public function expireOverdueSubscriptions(): int
    {
        $activeStatuses = SaasSubscriptionStatus::whereIn('code', ['active', 'trial'])->pluck('id');
        $expiredStatusId = SaasSubscriptionStatus::where('code', 'expired')->value('id');

        if (!$expiredStatusId) {
            return 0;
        }

        $count = GymSubscription::whereIn('saas_subscription_status_id', $activeStatuses)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->update(['saas_subscription_status_id' => $expiredStatusId]);

        return $count;
    }
}

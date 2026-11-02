<?php

namespace App\Services;

use App\Models\GymSubscription;
use App\Models\SaasPlan;
use App\Models\SaasSubscriptionStatus;
use Illuminate\Support\Facades\DB;

class SaasSubscriptionService
{
    public function createPlan(array $data): SaasPlan
    {
        return SaasPlan::create(array_merge($data, [
            'is_active' => true,
            'sort_order' => SaasPlan::count() + 1,
        ]));
    }

    public function updatePlan(SaasPlan $plan, array $data): SaasPlan
    {
        $plan->update($data);
        return $plan;
    }

    public function togglePlanStatus(SaasPlan $plan): SaasPlan
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return $plan;
    }

    public function assignSubscription(array $data): GymSubscription
    {
        return DB::transaction(function () use ($data) {
            $activeStatusId = SaasSubscriptionStatus::where('code', 'active')->value('id');
            $trialStatusId = SaasSubscriptionStatus::where('code', 'trial')->value('id');

            $trialDays = $data['trial_days'] ?? 0;
            $isTrial = $trialDays > 0;

            $subscription = GymSubscription::create([
                'gym_id'                      => $data['gym_id'],
                'saas_plan_id'                => $data['saas_plan_id'],
                'saas_subscription_status_id' => $isTrial ? $trialStatusId : $activeStatusId,
                'start_date'                  => $data['start_date'],
                'end_date'                    => $data['end_date'] ?? null,
                'trial_ends_at'               => $isTrial ? now()->addDays($trialDays) : null,
                'notes'                       => $data['notes'] ?? null,
            ]);

            return $subscription->load(['saasPlan', 'status', 'gym']);
        });
    }
    public function updateSubscription(GymSubscription $subscription, array $data): GymSubscription
    {
        return DB::transaction(function () use ($subscription, $data) {

            $updateData = [
                'saas_plan_id' => $data['saas_plan_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            if (isset($data['saas_subscription_status_id'])) {
                $updateData['saas_subscription_status_id'] = $data['saas_subscription_status_id'];
            }

            $subscription->update($updateData);

            return $subscription->load(['saasPlan', 'status', 'gym']);
        });
    }
    public function cancelSubscription(GymSubscription $subscription): GymSubscription
    {
        $cancelledStatusId = SaasSubscriptionStatus::where('code', 'cancelled')->value('id');
        $subscription->update(['saas_subscription_status_id' => $cancelledStatusId]);
        return $subscription;
    }

    public function getGymActiveSubscription(int $gymId): ?GymSubscription
    {
        return GymSubscription::forGym($gymId)
            ->active()
            ->with(['saasPlan', 'status'])
            ->latest('start_date')
            ->first();
    }
}

<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\MembershipStatus;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    public function assign(Gym $gym, Member $member, MembershipPlan $plan, string $startDate, ?string $notes = null): Membership
    {
        return DB::transaction(function () use ($gym, $member, $plan, $startDate, $notes) {

            if ($member->gym_id !== $gym->id || $plan->gym_id !== $gym->id) {
                abort(403, 'Member and plan must belong to the same gym.');
            }

            $start = \Carbon\Carbon::parse($startDate);
            $end = $start->copy()->addDays($plan->duration_days);

            $statusId = $start->isFuture()
                ? MembershipStatus::where('code', 'upcoming')->value('id')
                : MembershipStatus::where('code', 'active')->value('id');

            return Membership::create([
                'gym_id'               => $gym->id,
                'member_id'            => $member->id,
                'membership_plan_id'   => $plan->id,
                'membership_status_id' => $statusId,
                'start_date'           => $start->toDateString(),
                'end_date'             => $end->toDateString(),
                'price'                => $plan->price,
                'notes'                => $notes,
            ]);
        });
    }

    public function refreshStatuses(Gym $gym): int
    {
        $activeId = MembershipStatus::where('code', 'active')->value('id');
        $expiringId = MembershipStatus::where('code', 'expiring_soon')->value('id');
        $expiredId = MembershipStatus::where('code', 'expired')->value('id');

        $count = 0;

        // Mark expired
        $count += Membership::where('gym_id', $gym->id)
            ->whereIn('membership_status_id', [$activeId, $expiringId])
            ->where('end_date', '<', now())
            ->update(['membership_status_id' => $expiredId]);

        // Mark expiring soon (within 7 days)
        $count += Membership::where('gym_id', $gym->id)
            ->where('membership_status_id', $activeId)
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->update(['membership_status_id' => $expiringId]);

        return $count;
    }
}

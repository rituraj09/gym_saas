<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\Membership;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a specific user.
     */
    public function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?int $gymId = null,
        ?string $link = null
    ): AppNotification {
        // Prevent duplicate notifications of the same type on the same day
        $exists = AppNotification::where('user_id', $userId)
            ->where('type', $type)
            ->where('title', $title)
            ->whereDate('created_at', now())
            ->exists();

        if ($exists) {
            return AppNotification::where('user_id', $userId)
                ->where('type', $type)
                ->where('title', $title)
                ->whereDate('created_at', now())
                ->first();
        }

        return AppNotification::create([
            'user_id' => $userId,
            'gym_id' => $gymId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Notify all owners of a gym.
     */
    public function notifyGymOwners(
        Gym $gym,
        string $type,
        string $title,
        string $message,
        ?string $link = null
    ): int {
        $ownerIds = $gym->owners()->pluck('user_id');
        $count = 0;

        foreach ($ownerIds as $userId) {
            $this->notify($userId, $type, $title, $message, $gym->id, $link);
            $count++;
        }

        return $count;
    }

    /**
     * Notify all platform admins.
     */
    public function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?string $link = null
    ): int {
        $adminIds = User::whereHas('roles', fn($q) => $q->where('code', 'platform_admin'))
            ->pluck('id');
        $count = 0;

        foreach ($adminIds as $userId) {
            $this->notify($userId, $type, $title, $message, null, $link);
            $count++;
        }

        return $count;
    }

    /**
     * Generate membership expiration notifications for a gym.
     */
    public function generateMembershipNotifications(Gym $gym): int
    {
        $count = 0;

        // Expiring in 3 days
        $expiringSoon = Membership::where('gym_id', $gym->id)
            ->whereHas('status', fn($q) => $q->where('code', 'active'))
            ->whereBetween('end_date', [now()->addDays(1), now()->addDays(3)])
            ->with('member.user', 'membershipPlan')
            ->get();

        foreach ($expiringSoon as $membership) {
            $memberName = $membership->member?->user?->name ?? 'Unknown';
            $planName = $membership->membershipPlan?->name ?? 'Unknown';
            $days = (int) now()->startOfDay()->diffInDays($membership->end_date->startOfDay());

            $this->notifyGymOwners(
                $gym,
                AppNotification::TYPE_MEMBERSHIP_EXPIRING,
                "Membership expiring: {$memberName}",
                "{$memberName}'s {$planName} membership expires in {$days} day(s) on {$membership->end_date->format('M d, Y')}.",
                route('owner.memberships.index')
            );
            $count++;
        }

        // Expired today
        $expiredToday = Membership::where('gym_id', $gym->id)
            ->whereHas('status', fn($q) => $q->where('code', 'expired'))
            ->whereDate('end_date', now())
            ->with('member.user', 'membershipPlan')
            ->get();

        foreach ($expiredToday as $membership) {
            $memberName = $membership->member?->user?->name ?? 'Unknown';
            $planName = $membership->membershipPlan?->name ?? 'Unknown';

            $this->notifyGymOwners(
                $gym,
                AppNotification::TYPE_MEMBERSHIP_EXPIRED,
                "Membership expired: {$memberName}",
                "{$memberName}'s {$planName} membership has expired today.",
                route('owner.memberships.index')
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate SaaS subscription expiration notifications.
     */
    public function generateSubscriptionNotifications(): int
    {
        $count = 0;

        // Expiring in 7 days — notify gym owners
        $expiringSoon = GymSubscription::whereHas('status', fn($q) => $q->whereIn('code', ['active', 'trial']))
            ->whereBetween('end_date', [now()->addDays(1), now()->addDays(7)])
            ->with(['gym.owners', 'saasPlan'])
            ->get();

        foreach ($expiringSoon as $sub) {
            $days = (int) now()->startOfDay()->diffInDays($sub->end_date->startOfDay());
            $planName = $sub->saasPlan?->name ?? 'Unknown';

            // Notify gym owners
            $this->notifyGymOwners(
                $sub->gym,
                AppNotification::TYPE_SUBSCRIPTION_EXPIRING,
                "SaaS subscription expiring soon",
                "Your {$planName} subscription expires in {$days} day(s) on {$sub->end_date->format('M d, Y')}. Contact the platform admin to renew.",
                route('owner.dashboard')
            );

            // Notify admins
            $this->notifyAdmins(
                AppNotification::TYPE_SUBSCRIPTION_EXPIRING,
                "Subscription expiring: {$sub->gym->name}",
                "{$sub->gym->name}'s {$planName} subscription expires in {$days} day(s).",
                route('admin.gym-subscriptions.show', $sub)
            );
            $count++;
        }

        // Expired today — notify gym owners and admins
        $expiredToday = GymSubscription::whereHas('status', fn($q) => $q->where('code', 'expired'))
            ->whereDate('end_date', now())
            ->with(['gym.owners', 'saasPlan'])
            ->get();

        foreach ($expiredToday as $sub) {
            $planName = $sub->saasPlan?->name ?? 'Unknown';

            $this->notifyGymOwners(
                $sub->gym,
                AppNotification::TYPE_SUBSCRIPTION_EXPIRED,
                "SaaS subscription expired",
                "Your {$planName} subscription has expired. Access to the platform has been restricted. Contact the platform admin to renew.",
                route('owner.dashboard')
            );

            $this->notifyAdmins(
                AppNotification::TYPE_SUBSCRIPTION_EXPIRED,
                "Subscription expired: {$sub->gym->name}",
                "{$sub->gym->name}'s {$planName} subscription has expired today.",
                route('admin.gym-subscriptions.show', $sub)
            );
            $count++;
        }

        return $count;
    }

    /**
     * Generate all daily notifications.
     */
    public function generateAllDailyNotifications(): array
    {
        $stats = [
            'memberships' => 0,
            'subscriptions' => 0,
        ];

        // Membership notifications per gym
        $gyms = Gym::with('owners')->get();
        foreach ($gyms as $gym) {
            $stats['memberships'] += $this->generateMembershipNotifications($gym);
        }

        // Subscription notifications
        $stats['subscriptions'] = $this->generateSubscriptionNotifications();

        return $stats;
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(AppNotification $notification, int $userId): void
    {
        if ($notification->user_id === $userId) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete old read notifications (older than 30 days).
     */
    public function cleanupOldNotifications(): int
    {
        return AppNotification::where('is_read', true)
            ->where('created_at', '<', now()->subDays(30))
            ->forceDelete();
    }


    /**
     * Notify all trainers in a gym about a new member.
     */
    public static function notifyTrainersNewMember(int $gymId, string $memberName): void
    {
        $trainers = \App\Models\Trainer::where('gym_id', $gymId)->get();

        foreach ($trainers as $trainer) {
            self::create(
                $trainer->user_id,
                $gymId,
                'new_member',
                'New Member Joined',
                "{$memberName} has been added to your gym. You can now create exercise and diet plans for them."
            );
        }
    }

    /**
     * Notify a member that a new exercise plan has been created.
     */
    public static function notifyMemberExercisePlan(int $userId, int $gymId, string $planName, string $creatorName): void
    {
        self::create(
            $userId,
            $gymId,
            'exercise_plan_assigned',
            'New Exercise Plan',
            "{$creatorName} has created a new exercise plan for you: \"{$planName}\". Check your exercise plan to see the details."
        );
    }

    /**
     * Notify a member that a new diet plan has been created.
     */
    public static function notifyMemberDietPlan(int $userId, int $gymId, string $planName, string $creatorName): void
    {
        self::create(
            $userId,
            $gymId,
            'diet_plan_assigned',
            'New Diet Plan',
            "{$creatorName} has created a new diet plan for you: \"{$planName}\". Check your diet plan to see the details."
        );
    }

    /**
     * Notify a member about membership expiry.
     */
    public static function notifyMemberMembershipExpiring(int $userId, int $gymId, int $daysLeft): void
    {
        self::create(
            $userId,
            $gymId,
            'membership_expiring',
            'Membership Expiring Soon',
            "Your membership will expire in {$daysLeft} day" . ($daysLeft !== 1 ? 's' : '') . ". Please contact your gym to renew."
        );
    }

    /**
     * Notify a member that their membership has expired.
     */
    public static function notifyMemberMembershipExpired(int $userId, int $gymId): void
    {
        self::create(
            $userId,
            $gymId,
            'membership_expired',
            'Membership Expired',
            'Your membership has expired. Please contact your gym to renew and continue your fitness journey.'
        );
    }

    /**
     * Notify a member that their membership has been renewed.
     */
    public static function notifyMemberMembershipRenewed(int $userId, int $gymId, string $planName, string $endDate): void
    {
        self::create(
            $userId,
            $gymId,
            'membership_renewed',
            'Membership Renewed',
            "Your membership has been renewed! Plan: {$planName}, valid until {$endDate}."
        );
    }

    /**
     * Notify trainers about a member's membership expiry.
     */
    public static function notifyTrainersMemberExpired(int $gymId, int $memberId, string $memberName): void
    {
        $assignments = \App\Models\TrainerMemberAssignment::where('member_id', $memberId)
            ->whereHas('assignmentStatus', fn($q) => $q->where('code', 'active'))
            ->with('trainer')
            ->get();

        foreach ($assignments as $assignment) {
            if ($assignment->trainer) {
                self::create(
                    $assignment->trainer->user_id,
                    $gymId,
                    'member_membership_expired',
                    'Member Membership Expired',
                    "{$memberName}'s membership has expired. They may not be able to attend sessions until renewed."
                );
            }
        }
    }

    /**
     * Helper to create a notification.
     */
    protected static function create(int $userId, ?int $gymId, string $type, string $title, string $message): void
    {
        try {
            \App\Models\AppNotification::create([
                'user_id' => $userId,
                'gym_id'  => $gymId,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Notification creation failed: ' . $e->getMessage());
        }
    }
}

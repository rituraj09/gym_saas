<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Services\SaasSubscriptionService;

class DashboardController extends Controller
{
    public function __construct(
        protected SaasSubscriptionService $subscriptionService
    ) {}
    public function index(): View
    {
        $user = auth()->user();
        $gym = $user->gymOwner->gym;

        $stats = [
            'total_members'        => $gym->members()->count(),
            'active_members'       => 0, // Will be calculated in Phase 10
            'expiring_memberships' => 0, // Will be calculated in Phase 10
            'expired_memberships'  => 0, // Will be calculated in Phase 10
            'total_trainers'       => $gym->trainers()->count(),
            'recent_payments'      => collect(), // Will be populated in Phase 11
        ];
        $subscription = $this->subscriptionService->getGymActiveSubscription(
            auth()->user()->gymOwner->gym_id
        );
        return view('owner.dashboard', compact('gym', 'stats', 'subscription'));
    }
}

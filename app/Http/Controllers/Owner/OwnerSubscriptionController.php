<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionEnforcementService;

class OwnerSubscriptionController extends Controller
{
    public function expired(SubscriptionEnforcementService $enforcement)
    {
        $gym = auth()->user()->gymOwner->gym;
        $subscription = $gym->latestSubscription;

        return view('owner.subscription.subscription-expired', compact('gym', 'subscription'));
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('gym_owner')) {
            return $next($request);
        }

        $gym = $user->gymOwner?->gym;

        if (!$gym) {
            return $next($request);
        }

        $subscription = $gym->activeSubscription;

        // No subscription or inactive subscription → redirect to expired page
        if (!$subscription || !$subscription->isActive()) {
            // Allow access to the expired page itself and logout
            $allowedRoutes = [
                'owner.subscription-expired',
                'logout',
            ];

            if ($request->route() && in_array($request->route()->getName(), $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('owner.subscription-expired');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeature
{
    /**
     * @param string $feature  One of: exercise, diet, reports, priority_support
     */
    public function handle(Request $request, Closure $next, string $feature): Response
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

        if (!$subscription || !$subscription->hasFeature($feature)) {
            $featureLabels = [
                'exercise'         => 'Exercise Plans',
                'diet'             => 'Diet Plans',
                'reports'          => 'Reports',
                'priority_support' => 'Priority Support',
            ];

            $label = $featureLabels[$feature] ?? $feature;

            return redirect()
                ->route('owner.dashboard')
                ->with('error', "The \"{$label}\" feature is not available on your current plan. Please upgrade your subscription.");
        }

        return $next($request);
    }
}

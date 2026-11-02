<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        if (! $member) {
            abort(403, 'Member profile not found.');
        }

        $member->load([
            'activeMembership.membershipPlan',
            'activeMembership.membershipStatus',
            'activeExercisePlan.workoutDays.exercises.exercise',
            'activeDietPlan.meals.mealType',
            'activeDietPlan.meals.items',
            'gym',
        ]);

        $todayDayName = now()->format('l');

        $todayWorkout = null;
        if ($member->activeExercisePlan) {
            $todayWorkout = $member->activeExercisePlan->workoutDays
                ->first(fn ($d) => strcasecmp($d->day_name, $todayDayName) === 0);
        }

        $unreadNotifications = $user->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();

        return view('member.dashboard', compact('member', 'todayWorkout', 'todayDayName', 'unreadNotifications'));
    }
}

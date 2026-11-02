<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberExercisePlan;
use App\Models\MemberDietPlan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $trainer = $user->trainer;

        if (! $trainer) {
            abort(403, 'Trainer profile not found.');
        }

        $gymId = $trainer->gym_id;
        $assignedIds = $trainer->getAssignedMemberIds();

        $stats = [
            'total_gym_members' => Member::where('gym_id', $gymId)->count(),
            'my_members'        => count($assignedIds),
            'active_exercise'   => MemberExercisePlan::where('gym_id', $gymId)->where('is_active', true)->count(),
            'active_diet'       => MemberDietPlan::where('gym_id', $gymId)->where('is_active', true)->count(),
        ];

        $assignedMemberIds = $trainer->getAssignedMemberIds();

        $recentPlans = MemberExercisePlan::with(['member.user'])
            ->where('gym_id', $gymId)
            ->whereIn('member_id', $assignedMemberIds)
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = $user->unreadNotificationCount();

        return view('trainer.dashboard', compact('trainer', 'stats', 'recentPlans', 'unreadCount'));
    }
}

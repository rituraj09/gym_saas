<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MemberController extends Controller
{
    /**
     * View all members in the trainer's gym.
     */
    public function index()
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        $assignedIds = $trainer->getAssignedMemberIds();

        $members = Member::with([
                'user',
                'gender',
                'activeMembership.plan',
                'activeMembership.status',
                'activeExercisePlan',
                'activeDietPlan',
            ])
            ->where('gym_id', $trainer->gym_id)
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('members.*')
            ->get();

        return view('trainer.members.index', compact(
            'members',
            'assignedIds'
        ));
    }

    /**
     * View a single member's details.
     */


    public function show(string $encryptedId)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        // Decrypt — pass `false` if you used encryptString() on the other side
        try {
            $memberId = decrypt($encryptedId, false);
        } catch (DecryptException $e) {
            abort(404, 'Invalid member identifier.');
        }

        $member = Member::find($memberId);

        if (! $member) {
            abort(404, 'Member not found.');
        }

        if (! $trainer->canManageMember($member->id)) {
            abort(403, 'This member is not in your gym.');
        }

        $member->load([
            'user',
            'gender',
            'bloodGroup',
            'activeMembership.plan',
            'activeMembership.status',
            'activeExercisePlan.workoutDays.exerciseTemplate.items.exercise',
            'activeDietPlan.dietPlanTemplate.meals.mealType',
            'activeDietPlan.dietPlanTemplate.meals.items',
        ]);

        $isAssigned = in_array(
            $member->id,
            $trainer->getAssignedMemberIds()
        );

        return view('trainer.members.show', compact('member', 'isAssigned'));
    }
}

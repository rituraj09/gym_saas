<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\AssignTrainerRequest;
use App\Models\Member;
use App\Models\Trainer;
use App\Services\AssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    /**
     * Show the assignment form for a specific member.
     */
    public function createForMember(Member $member): View
    {
        $this->authorizeGymMember($member);

        $gym = auth()->user()->gymOwner->gym;

        $trainers = Trainer::with('user')
            ->where('gym_id', $gym->id)
            ->whereHas('user', fn ($q) => $q->whereHas('accountStatus', fn ($s) => $s->where('code', 'active')))
            ->get();

        $currentTrainer = $this->assignmentService->getCurrentTrainer($gym, $member);
        $history = $this->assignmentService->getHistory($gym, $member);

        return view('owner.assignments.create', compact('member', 'trainers', 'currentTrainer', 'history'));
    }

    /**
     * Store a new assignment.
     */
    public function store(AssignTrainerRequest $request): RedirectResponse
    {
        $gym = $request->user()->gymOwner->gym;

        $member = Member::findOrFail($request->member_id);
        $trainer = Trainer::findOrFail($request->trainer_id);

        $this->authorizeGymMember($member);
        $this->authorizeGymTrainer($trainer);

        $this->assignmentService->assign($gym, $member, $trainer, $request->start_date);

        return redirect()->route('owner.members.show', $member)
            ->with('success', 'Trainer assigned successfully.');
    }

    /**
     * Unassign the current trainer from a member.
     */
    public function unassign(Member $member): RedirectResponse
    {
        $this->authorizeGymMember($member);

        $gym = auth()->user()->gymOwner->gym;

        $this->assignmentService->unassign($gym, $member);

        return back()->with('success', 'Trainer unassigned successfully.');
    }

    protected function authorizeGymMember(Member $member): void
    {
        if ($member->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403, 'This member does not belong to your gym.');
        }
    }

    protected function authorizeGymTrainer(Trainer $trainer): void
    {
        if ($trainer->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403, 'This trainer does not belong to your gym.');
        }
    }
}

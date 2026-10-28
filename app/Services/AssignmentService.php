<?php

namespace App\Services;

use App\Models\AssignmentStatus;
use App\Models\Gym;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\TrainerMemberAssignment;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * Assign a trainer to a member.
     * If the member already has an active assignment, close it first (transferred).
     */
    public function assign(Gym $gym, Member $member, Trainer $trainer, string $startDate): TrainerMemberAssignment
    {
        return DB::transaction(function () use ($gym, $member, $trainer, $startDate) {

            // Validate both belong to the same gym
            if ($member->gym_id !== $gym->id || $trainer->gym_id !== $gym->id) {
                abort(403, 'Trainer and member must belong to the same gym.');
            }

            $activeStatusId = AssignmentStatus::where('code', 'active')->value('id');
            $transferredStatusId = AssignmentStatus::where('code', 'transferred')->value('id');

            // Close any existing active assignment for this member
            $existingActive = TrainerMemberAssignment::where('gym_id', $gym->id)
                ->where('member_id', $member->id)
                ->where('assignment_status_id', $activeStatusId)
                ->first();

            if ($existingActive) {
                $existingActive->update([
                    'assignment_status_id' => $transferredStatusId,
                    'end_date'             => now()->toDateString(),
                ]);
            }

            // Create new active assignment
            return TrainerMemberAssignment::create([
                'gym_id'               => $gym->id,
                'trainer_id'           => $trainer->id,
                'member_id'            => $member->id,
                'assignment_status_id' => $activeStatusId,
                'start_date'           => $startDate,
            ]);
        });
    }

    /**
     * Unassign the current trainer from a member (close without transfer).
     */
    public function unassign(Gym $gym, Member $member): bool
    {
        $activeStatusId = AssignmentStatus::where('code', 'active')->value('id');
        $closedStatusId = AssignmentStatus::where('code', 'closed')->value('id');

        $active = TrainerMemberAssignment::where('gym_id', $gym->id)
            ->where('member_id', $member->id)
            ->where('assignment_status_id', $activeStatusId)
            ->first();

        if (! $active) {
            return false;
        }

        $active->update([
            'assignment_status_id' => $closedStatusId,
            'end_date'             => now()->toDateString(),
        ]);

        return true;
    }

    /**
     * Get the current active trainer for a member.
     */
    public function getCurrentTrainer(Gym $gym, Member $member): ?Trainer
    {
        $assignment = TrainerMemberAssignment::with('trainer.user')
            ->where('gym_id', $gym->id)
            ->where('member_id', $member->id)
            ->active()
            ->first();

        return $assignment?->trainer;
    }

    /**
     * Get assignment history for a member.
     */
    public function getHistory(Gym $gym, Member $member)
    {
        return TrainerMemberAssignment::with(['trainer.user', 'status'])
            ->where('gym_id', $gym->id)
            ->where('member_id', $member->id)
            ->orderByDesc('start_date')
            ->get();
    }

    /**
     * Get all active members assigned to a trainer.
     */
    public function getTrainerMembers(Gym $gym, Trainer $trainer)
    {
        return TrainerMemberAssignment::with(['member.user', 'member.gender'])
            ->where('gym_id', $gym->id)
            ->where('trainer_id', $trainer->id)
            ->active()
            ->get()
            ->pluck('member');
    }
}

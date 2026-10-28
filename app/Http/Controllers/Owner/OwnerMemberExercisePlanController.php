<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMemberExercisePlanRequest;
use App\Http\Requests\Owner\UpdateMemberExercisePlanRequest;
use App\Models\ExerciseTemplate;
use App\Models\Member;
use App\Models\MemberExercisePlan;
use App\Models\MemberWorkoutLog;
use App\Models\WorkoutLogStatus;
use App\Services\MemberExercisePlanService;
use Illuminate\Http\Request;

class OwnerMemberExercisePlanController extends Controller
{
    public function __construct(
        protected MemberExercisePlanService $planService
    ) {}

    protected function getGymId(): int
    {
        return auth()->user()->gymOwner->gym_id;
    }

    protected function authorizeGym(MemberExercisePlan $plan): void
    {
        if ($plan->gym_id !== $this->getGymId()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $query = MemberExercisePlan::with(['member', 'workoutDays'])
            ->forGym($this->getGymId())
            ->orderBy('created_at', 'desc');

        if ($request->filled('member_id')) {
            $query->forMember($request->member_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $plans = $query->paginate(12)->withQueryString();
        $members = Member::where('gym_id', $this->getGymId())
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('members.*', 'users.name')
            ->get();

        return view('owner.member-exercise-plans.index', compact('plans', 'members'));
    }

    public function create()
    {
        $members = Member::where('gym_id', $this->getGymId())
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
             ->select('members.*', 'users.name')
            ->get();

        $templates = ExerciseTemplate::active()
            ->forGym($this->getGymId())
            ->with('targetMuscleGroup')
            ->orderBy('name')
            ->get();

        return view('owner.member-exercise-plans.create', compact('members', 'templates'));
    }

    public function store(StoreMemberExercisePlanRequest $request)
    {
        // Verify member belongs to this gym
        $member = Member::where('id', $request->member_id)
            ->where('gym_id', $this->getGymId())
            ->firstOrFail();

        $plan = $this->planService->create(
            auth()->user()->gymOwner->gym,
            auth()->id(),
            $request->validated()
        );

        return redirect()
            ->route('owner.member-exercise-plans.show', $plan)
            ->with('success', 'Exercise plan created and assigned to ' . $member->full_name . '.');
    }

    public function show(MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->load([
            'member',
            'workoutDays.exerciseTemplate.items.exercise',
            'createdBy',
        ]);

        // Get this week's logs
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $weeklyLogs = MemberWorkoutLog::where('member_exercise_plan_id', $plan->id)
            ->whereBetween('workout_date', [$startOfWeek, $endOfWeek])
            ->with('status')
            ->get()
            ->groupBy(fn($log) => $log->workout_date->toDateString());

        $dayNames = [
            1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
        ];

        $today = now()->dayOfWeekIso; // 1=Mon, 7=Sun

        return view('owner.member-exercise-plans.show', compact(
            'plan', 'weeklyLogs', 'dayNames', 'today', 'startOfWeek', 'endOfWeek'
        ));
    }

    public function edit(MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->load('workoutDays');

       $members = Member::where('gym_id', $this->getGymId())
        ->leftJoin('users', 'members.user_id', '=', 'users.id')
        ->orderBy('users.name')
          ->select('members.*', 'users.name')
        ->get();

        $templates = ExerciseTemplate::active()
            ->forGym($this->getGymId())
            ->with('targetMuscleGroup')
            ->orderBy('name')
            ->get();

        return view('owner.member-exercise-plans.edit', compact('plan', 'members', 'templates'));
    }

    public function update(UpdateMemberExercisePlanRequest $request, MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $this->planService->update($plan, $request->validated());

        return redirect()
            ->route('owner.member-exercise-plans.show', $plan)
            ->with('success', 'Exercise plan updated successfully.');
    }

    public function toggleStatus(MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $this->planService->toggleStatus($plan);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Plan {$status} successfully.");
    }

    public function destroy(MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->delete();

        return redirect()
            ->route('owner.member-exercise-plans.index')
            ->with('success', 'Exercise plan deleted successfully.');
    }

    public function logWorkout(Request $request, MemberExercisePlan $plan)
    {
        $this->authorizeGym($plan);

        $request->validate([
            'workout_day_id' => 'required|exists:member_workout_days,id',
            'workout_date'   => 'required|date',
            'status'         => 'required|in:completed,skipped',
            'notes'          => 'nullable|string|max:500',
        ]);

        $this->planService->logWorkout(
            $plan,
            $request->workout_day_id,
            $request->workout_date,
            $request->status,
            $request->notes
        );

        return back()->with('success', 'Workout logged successfully.');
    }
}

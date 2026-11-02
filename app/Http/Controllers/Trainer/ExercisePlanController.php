<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseTemplate;
use App\Models\Member;
use App\Models\MemberExercisePlan;
use App\Models\MemberWorkoutDay;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ExercisePlanController extends Controller
{
    private function trainer()
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        return $trainer;
    }

    private function authorizePlan(MemberExercisePlan $plan, $trainer): void
    {
        if ($plan->gym_id != $trainer->gym_id) {
            abort(403);
        }
    }

    private function formData()
    {
        $trainer = $this->trainer();

        $members = Member::with('user')
            ->where('gym_id', $trainer->gym_id)
            ->whereHas('user')
            ->get()
            ->sortBy(fn ($member) => $member->user->name)
            ->values();

        $exercises = Exercise::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return compact('members', 'exercises');
    }

    public function index()
    {
        $trainer = $this->trainer();

        $plans = MemberExercisePlan::with('member.user')
            ->where('gym_id', $trainer->gym_id)
            ->withCount('workoutDays')
            ->latest()
            ->paginate(15);

        return view('trainer.exercise-plans.index', compact('plans'));
    }

    public function create()
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        $members = Member::where('gym_id', $trainer->gym_id)
            ->with('user')
            ->get();

        $exerciseTemplates = ExerciseTemplate::where('gym_id', $trainer->gym_id)
            ->with('items.exercise')
            ->orderBy('name')
            ->get();

        return view('trainer.exercise-plans.create', compact(
            'members',
            'exerciseTemplates'
        ));
    }

    public function store(Request $request)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        // Validate submitted data and restrict records to this gym.
        $validated = $request->validate([
            'member_id' => [
                'required',
                Rule::exists('members', 'id')
                    ->where('gym_id', $trainer->gym_id)
                    ->whereNull('deleted_at'),
            ],

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'start_date' => ['required', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'days' => ['required', 'array', 'min:1'],

            'days.*.day_of_week' => [
                'required',
                'integer',
                'between:1,7',
                'distinct',
            ],

            'days.*.exercise_template_id' => [
                'required',
                Rule::exists('exercise_templates', 'id')
                    ->where('gym_id', $trainer->gym_id)
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],

            'days.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Get the member belonging to this gym.
        $member = Member::with('user')
            ->where('gym_id', $trainer->gym_id)
            ->findOrFail($validated['member_id']);

        // Create plan and workout days in one database transaction.
        $plan = DB::transaction(function () use ($validated, $trainer, $member) {

            // Deactivate the member's previous active exercise plan.
            MemberExercisePlan::where('gym_id', $trainer->gym_id)
                ->where('member_id', $member->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                ]);

            // Create the exercise plan.
            $plan = MemberExercisePlan::create([
                'gym_id' => $trainer->gym_id,
                'member_id' => $member->id,
                'created_by_user_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'is_active' => true,
            ]);

            // Create workout days with their selected exercise templates.
            foreach ($validated['days'] as $index => $day) {

                $plan->workoutDays()->create([
                    'day_of_week' => $day['day_of_week'],
                    'exercise_template_id' => $day['exercise_template_id'],
                    'notes' => $day['notes'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            return $plan;
        });

        // Notify the member after successful database transaction.
        if ($member->user) {
            NotificationService::notifyMemberExercisePlan(
                $member->user_id,
                $trainer->gym_id,
                $plan->name,
                Auth::user()->name
            );
        }

        return redirect()
            ->route('trainer.exercise-plans.index')
            ->with('success', 'Exercise plan created and member notified.');
    }

    public function edit(string $encryptedId)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        try {
            $planId = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $exercise_plan = MemberExercisePlan::with([
            'member.user',
            'workoutDays.exerciseTemplate.items.exercise',
        ])
            ->where('gym_id', $trainer->gym_id)
            ->findOrFail($planId);

        $members = Member::with('user')
            ->where('gym_id', $trainer->gym_id)
            ->get();

        $exerciseTemplates = ExerciseTemplate::forGym($trainer->gym_id)
            ->active()
            ->with('items.exercise')
            ->orderBy('name')
            ->get();

        return view('trainer.exercise-plans.edit', compact(
            'exercise_plan',
            'members',
            'exerciseTemplates'
        ));
    }

    public function update(Request $request, string $encryptedId)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        // Decrypt the exercise plan ID.
        try {
            $planId = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        // Verify that the plan belongs to the trainer's gym.
        $exercise_plan = MemberExercisePlan::where('gym_id', $trainer->gym_id)
            ->findOrFail($planId);

        $validated = $request->validate([
            'member_id' => [
                'required',
                Rule::exists('members', 'id')
                    ->where('gym_id', $trainer->gym_id)
                    ->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'days' => ['required', 'array', 'min:1'],
            'days.*.day_of_week' => [
                'required',
                'integer',
                'between:1,7',
                'distinct',
            ],
            'days.*.exercise_template_id' => [
                'required',
                Rule::exists('exercise_templates', 'id')
                    ->where('gym_id', $trainer->gym_id)
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],
            'days.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $member = Member::where('gym_id', $trainer->gym_id)
            ->with('user')
            ->findOrFail($validated['member_id']);

        DB::transaction(function () use ($validated, $trainer, $exercise_plan, $member) {

            // Deactivate other active plans for this member.
            MemberExercisePlan::where('gym_id', $trainer->gym_id)
                ->where('member_id', $member->id)
                ->where('id', '!=', $exercise_plan->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Update the exercise plan.
            $exercise_plan->update([
                'member_id' => $member->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
            ]);

            // Replace existing workout days.
            $exercise_plan->workoutDays()->delete();

            foreach ($validated['days'] as $index => $day) {
                $exercise_plan->workoutDays()->create([
                    'day_of_week' => $day['day_of_week'],
                    'exercise_template_id' => $day['exercise_template_id'],
                    'notes' => $day['notes'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
        });

        // Notify the assigned member.
        if ($member->user) {
            NotificationService::notifyMemberExercisePlan(
                $member->user_id,
                $trainer->gym_id,
                $exercise_plan->name,
                Auth::user()->name
            );
        }

        return redirect()
            ->route('trainer.exercise-plans.index')
            ->with('success', 'Exercise plan updated successfully.');
    }

    private function validatePlan(Request $request, $trainer): array
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'plan_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',

            'days' => 'required|array|min:1',
            'days.*.day_of_week' => 'required|integer|between:1,7',
            'days.*.notes' => 'nullable|string|max:1000',

            'days.*.exercises' => 'required|array|min:1',
            'days.*.exercises.*.exercise_id' => 'required|integer|exists:exercises,id',
            'days.*.exercises.*.sets' => 'required|integer|min:1|max:20',
            'days.*.exercises.*.reps' => 'required|integer|min:1|max:100',
            'days.*.exercises.*.rest_seconds' => 'nullable|integer|min:0|max:600',
        ]);

        $memberExists = Member::where('id', $validated['member_id'])
            ->where('gym_id', $trainer->gym_id)
            ->exists();

        if (! $memberExists) {
            abort(403, 'This member does not belong to your gym.');
        }

        $exerciseIds = collect($validated['days'])
            ->flatMap(fn ($day) => collect($day['exercises'])->pluck('exercise_id'))
            ->unique();

        $activeExerciseIds = Exercise::active()
            ->whereIn('id', $exerciseIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($exerciseIds->diff($activeExerciseIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'days' => 'One or more selected exercises are inactive or unavailable.',
            ]);
        }

        return $validated;
    }

    private function saveWorkoutDays(
        MemberExercisePlan $plan,
        array $days,
        $trainer
    ): void {
        foreach ($days as $index => $day) {

            // Create one private template for each workout day.
            $template = ExerciseTemplate::create([
                'gym_id' => $trainer->gym_id,
                'created_by_user_id' => Auth::id(),
                'name' => $plan->name . ' - Day ' . ($index + 1),
                'description' => $day['notes'] ?? null,
                'is_active' => true,
            ]);

            $workoutDay = MemberWorkoutDay::create([
                'member_exercise_plan_id' => $plan->id,
                'day_of_week' => $day['day_of_week'],
                'exercise_template_id' => $template->id,
                'notes' => $day['notes'] ?? null,
                'sort_order' => $index + 1,
            ]);

            foreach ($day['exercises'] as $exerciseIndex => $exercise) {
                $template->items()->create([
                    'exercise_id' => $exercise['exercise_id'],
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'rest_seconds' => $exercise['rest_seconds'] ?? 60,
                    'sort_order' => $exerciseIndex + 1,
                ]);
            }
        }
    }

    public function show(string $encryptedId)
    {
        $trainer = Auth::user()->trainer;

        if (! $trainer) {
            abort(403);
        }

        try {
            $planId = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $exercise_plan = MemberExercisePlan::with([
            'member.user',
            'workoutDays.exerciseTemplate.items.exercise',
        ])
            ->where('gym_id', $trainer->gym_id)
            ->findOrFail($planId);

        return view('trainer.exercise-plans.show', compact('exercise_plan'));
    }

    public function toggleActive(string $encryptedId)
    {
        $trainer = $this->trainer();

        try {
            $planId = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $exercise_plan = MemberExercisePlan::where('gym_id', $trainer->gym_id)
            ->findOrFail($planId);

        $this->authorizePlan($exercise_plan, $trainer);

        DB::transaction(function () use ($exercise_plan, $trainer) {

            if (! $exercise_plan->is_active) {

                // Deactivate all other active plans for this member.
                MemberExercisePlan::where('gym_id', $trainer->gym_id)
                    ->where('member_id', $exercise_plan->member_id)
                    ->where('id', '!=', $exercise_plan->id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                    ]);

                // Activate the selected plan.
                $exercise_plan->update([
                    'is_active' => true,
                ]);

            } else {

                // Simply deactivate the selected plan.
                $exercise_plan->update([
                    'is_active' => false,
                ]);
            }
        });

        return back()->with('success', 'Plan status updated successfully.');
    }

    public function destroy(string $encryptedId)
    {
        $trainer = $this->trainer();

        try {
            $planId = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $exercise_plan = MemberExercisePlan::where('gym_id', $trainer->gym_id)
            ->findOrFail($planId);

        $this->authorizePlan($exercise_plan, $trainer);

        DB::transaction(function () use ($exercise_plan) {

            // Soft delete associated workout days.
            $exercise_plan->workoutDays()->delete();

            // Soft delete the exercise plan.
            $exercise_plan->delete();
        });

        return redirect()
            ->route('trainer.exercise-plans.index')
            ->with('success', 'Exercise plan deleted successfully.');
    }
}

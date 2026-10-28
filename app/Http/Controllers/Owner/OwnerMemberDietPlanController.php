<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMemberDietPlanRequest;
use App\Models\DietPlanTemplate;
use App\Models\Member;
use App\Models\MemberDietPlan;
use App\Models\MemberDietLog;
use App\Services\DietPlanService;
use Illuminate\Http\Request;

class OwnerMemberDietPlanController extends Controller
{
    public function __construct(
        protected DietPlanService $dietPlanService
    ) {}

    protected function getGymId(): int
    {
        return auth()->user()->gymOwner->gym_id;
    }

    protected function authorizeGym(MemberDietPlan $plan): void
    {
        if ($plan->gym_id !== $this->getGymId()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $query = MemberDietPlan::with(['member', 'dietPlanTemplate'])
            ->forGym($this->getGymId())
            ->orderBy('created_at', 'desc');

        if ($request->filled('member_id')) {
            $query->forMember($request->member_id);
        }

        $plans = $query->paginate(12)->withQueryString();

        $members = Member::where('gym_id', $this->getGymId())
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('members.*')
            ->get();

        return view('owner.member-diet-plans.index', compact('plans', 'members'));
    }

    public function create()
    {
        $members = Member::where('gym_id', $this->getGymId())
            ->leftJoin('users', 'members.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('members.*')
            ->get();

        $templates = DietPlanTemplate::active()
            ->forGym($this->getGymId())
            ->orderBy('name')
            ->get();

        return view('owner.member-diet-plans.create', compact('members', 'templates'));
    }

    public function store(StoreMemberDietPlanRequest $request)
    {
        $member = Member::where('id', $request->member_id)
            ->where('gym_id', $this->getGymId())
            ->firstOrFail();

        $plan = $this->dietPlanService->assignToMember(
            auth()->user()->gymOwner->gym,
            auth()->id(),
            $request->validated()
        );

        return redirect()
            ->route('owner.member-diet-plans.show', $plan)
            ->with('success', 'Diet plan assigned to ' . $member->user->name . '.');
    }

    public function show(MemberDietPlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->load([
            'member.user',
            'dietPlanTemplate.meals.items',
            'dietPlanTemplate.meals.mealType',
            'createdBy',
        ]);

        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();

        $weeklyLogs = MemberDietLog::where('member_diet_plan_id', $plan->id)
            ->whereBetween('log_date', [$startOfWeek, $endOfWeek])
            ->get()
            ->keyBy(fn($log) => $log->log_date->toDateString());

        $today = now()->dayOfWeekIso;

        return view('owner.member-diet-plans.show', compact(
            'plan', 'weeklyLogs', 'today', 'startOfWeek', 'endOfWeek'
        ));
    }

    public function toggleStatus(MemberDietPlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Diet plan {$status} successfully.");
    }

    public function destroy(MemberDietPlan $plan)
    {
        $this->authorizeGym($plan);

        $plan->delete();

        return redirect()
            ->route('owner.member-diet-plans.index')
            ->with('success', 'Diet plan removed successfully.');
    }

    public function logDiet(Request $request, MemberDietPlan $plan)
    {
        $this->authorizeGym($plan);

        $request->validate([
            'log_date' => 'required|date',
            'status'   => 'required|in:followed,partially,not_followed',
            'notes'    => 'nullable|string|max:500',
        ]);

        $this->dietPlanService->logDiet(
            $plan,
            $request->log_date,
            $request->status,
            $request->notes
        );

        return back()->with('success', 'Diet log recorded successfully.');
    }
}

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMembershipPlanRequest;
use App\Http\Requests\Owner\UpdateMembershipPlanRequest;
use App\Models\MembershipPlan;
use App\Services\MembershipPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipPlanController extends Controller
{
    public function __construct(
        protected MembershipPlanService $planService
    ) {}

    public function index(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $query = MembershipPlan::where('gym_id', $gym->id);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $plans = $query->latest()->paginate(15)->withQueryString();

        return view('owner.membership-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('owner.membership-plans.create');
    }

    public function store(StoreMembershipPlanRequest $request): RedirectResponse
    {
        $gym = $request->user()->gymOwner->gym;

        $this->planService->create($gym, $request->validated());

        return redirect()->route('owner.membership-plans.index')
            ->with('success', 'Membership plan created successfully.');
    }

    public function show(MembershipPlan $membershipPlan): View
    {
        $this->authorizeGym($membershipPlan);
        $membershipPlan->loadCount('memberships');

        return view('owner.membership-plans.show', compact('membershipPlan'));
    }

    public function edit(MembershipPlan $membershipPlan): View
    {
        $this->authorizeGym($membershipPlan);

        return view('owner.membership-plans.edit', compact('membershipPlan'));
    }

    public function update(UpdateMembershipPlanRequest $request, MembershipPlan $membershipPlan): RedirectResponse
    {
        $this->authorizeGym($membershipPlan);

        $this->planService->update($membershipPlan, $request->validated());

        return redirect()->route('owner.membership-plans.show', $membershipPlan)
            ->with('success', 'Plan updated successfully.');
    }

    public function toggleStatus(MembershipPlan $membershipPlan): RedirectResponse
    {
        $this->authorizeGym($membershipPlan);

        $isActive = $this->planService->toggleStatus($membershipPlan);

        $status = $isActive ? 'activated' : 'deactivated';

        return back()->with('success', "Plan {$status} successfully.");
    }

    protected function authorizeGym(MembershipPlan $plan): void
    {
        if ($plan->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403);
        }
    }
}

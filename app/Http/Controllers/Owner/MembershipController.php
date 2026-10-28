<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMembershipRequest;
use App\Models\Member;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function __construct(
        protected MembershipService $membershipService
    ) {}

    public function index(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $this->membershipService->refreshStatuses($gym);

        $query = Membership::with(['member.user', 'plan', 'status'])
            ->where('gym_id', $gym->id);

        if ($request->filled('search')) {
            $query->whereHas('member.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('status', fn ($q) => $q->where('code', $request->status));
        }

        $memberships = $query->latest('start_date')->paginate(15)->withQueryString();

        return view('owner.memberships.index', compact('memberships'));
    }

    public function create(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $members = Member::with('user')
            ->where('gym_id', $gym->id)
            ->get();

        $plans = MembershipPlan::where('gym_id', $gym->id)->active()->get();

        $selectedMember = $request->filled('member_id') ? Member::find($request->member_id) : null;

        return view('owner.memberships.create', compact('members', 'plans', 'selectedMember'));
    }

    public function store(StoreMembershipRequest $request): RedirectResponse
    {
        $gym = $request->user()->gymOwner->gym;
        $member = Member::findOrFail($request->member_id);
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);

        $this->membershipService->assign($gym, $member, $plan, $request->start_date, $request->notes);

        return redirect()->route('owner.memberships.index')
            ->with('success', 'Membership assigned successfully.');
    }

    public function show(Membership $membership): View
    {
        if ($membership->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403);
        }

        $membership->load(['member.user', 'plan', 'status', 'payments']);

        return view('owner.memberships.show', compact('membership'));
    }

    protected function authorizeGym(Membership $membership): void
    {
        if ($membership->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403);
        }
    }
}

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreMemberRequest;
use App\Http\Requests\Owner\UpdateMemberRequest;
use App\Models\BloodGroupType;
use App\Models\GenderType;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        protected MemberService $memberService
    ) {}

    public function index(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $query = Member::with(['user.accountStatus', 'gender'])
            ->where('gym_id', $gym->id);

        if ($search = $request->filled('search') ? $request->search : null) {
            $query->where(function ($q) use ($search) {
                $q->where('member_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->whereHas('accountStatus', fn ($sq) => $sq->where('code', $request->status));
            });
        }

        $members = $query->latest('joining_date')->paginate(15)->withQueryString();

        return view('owner.members.index', compact('members', 'gym'));
    }

    public function create(): View
    {
        $genders = GenderType::orderBy('name')->get();
        $bloodGroups = BloodGroupType::orderBy('name')->get();

        return view('owner.members.create', compact('genders', 'bloodGroups'));
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $gym = auth()->user()->gymOwner->gym;

        if ($subscription && !$subscription->canAddMember()) {
            return back()->with('error', 'You have reached the maximum number of members (' . $subscription->saasPlan->max_members_label . ') allowed by your ' . $subscription->saasPlan->name . ' plan. Please upgrade to add more.');
        }

        $this->memberService->createMember(
            $gym,
            $request->validated(),
            $request->file('profile_photo')
        );

        return redirect()->route('owner.members.index')
            ->with('success', 'Member added successfully.');
    }

    public function show(Member $member): View
    {
        $this->authorizeGym($member);

        $member->load(['user.accountStatus', 'gender', 'bloodGroup', 'gym']);

        return view('owner.members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $this->authorizeGym($member);

        $member->load(['user', 'gender', 'bloodGroup']);
        $genders = GenderType::orderBy('name')->get();
        $bloodGroups = BloodGroupType::orderBy('name')->get();

        return view('owner.members.edit', compact('member', 'genders', 'bloodGroups'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $this->authorizeGym($member);

        $this->memberService->updateMember(
            $member,
            $request->validated(),
            $request->file('profile_photo')
        );

        return redirect()->route('owner.members.show', $member)
            ->with('success', 'Member updated successfully.');
    }

    public function toggleStatus(Member $member): RedirectResponse
    {
        $this->authorizeGym($member);

        $action = $this->memberService->toggleStatus($member);

        return back()->with('success', "Member {$action} successfully.");
    }

    protected function authorizeGym(Member $member): void
    {
        $gymId = auth()->user()->gymOwner->gym_id;

        if ($member->gym_id !== $gymId) {
            abort(403, 'This member does not belong to your gym.');
        }
    }
}

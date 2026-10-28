<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePaymentRequest;
use App\Models\GymMemberPayment;
use App\Models\Member;
use App\Models\Membership;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $query = GymMemberPayment::with(['member.user', 'method', 'status', 'membership.plan'])
            ->where('gym_id', $gym->id);

        if ($request->filled('search')) {
            $query->whereHas('member.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();

        $totalAmount = GymMemberPayment::where('gym_id', $gym->id)
            ->whereHas('status', fn ($q) => $q->where('code', 'completed'))
            ->sum('amount');

        return view('owner.payments.index', compact('payments', 'totalAmount'));
    }

    public function create(Request $request): View
    {
        $gym = $request->user()->gymOwner->gym;

        $members = Member::with('user')->where('gym_id', $gym->id)->get();
        $methods = PaymentMethod::orderBy('name')->get();

        $selectedMember = $request->filled('member_id') ? Member::with('memberships.plan')->find($request->member_id) : null;

        return view('owner.payments.create', compact('members', 'methods', 'selectedMember'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $gym = $request->user()->gymOwner->gym;

        $this->paymentService->record($gym, $request->validated(), $request->file('receipt'));

        return redirect()->route('owner.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(GymMemberPayment $payment): View
    {
        if ($payment->gym_id !== auth()->user()->gymOwner->gym_id) {
            abort(403);
        }

        $payment->load(['member.user', 'method', 'status', 'membership.plan']);

        return view('owner.payments.show', compact('payment'));
    }
}

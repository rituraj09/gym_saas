<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaasPaymentRequest;
use App\Models\GymSubscription;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\SaasPayment;
use App\Services\SaasPaymentService;
use Illuminate\Http\Request;

class AdminSaasPaymentController extends Controller
{
    public function __construct(
        protected SaasPaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $query = SaasPayment::with([
                'subscription.gym',
                'subscription.saasPlan',
                'paymentMethod',
                'paymentStatus',
                'recordedByUser',
            ])
            ->orderBy('payment_date', 'desc');

        // Filter by gym
        if ($request->filled('gym_id')) {
            $query->whereHas('subscription', fn($q) => $q->where('gym_id', $request->gym_id));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('paymentStatus', fn($q) => $q->where('code', $request->status));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->withQueryString();
        $subscriptions = GymSubscription::with('gym')->get();
        $statuses = PaymentStatus::orderBy('name')->get();

        // Summary stats
        $totalCollected = SaasPayment::whereHas('paymentStatus', fn($q) => $q->where('code', 'completed'))
            ->sum('amount');
        $pendingAmount = SaasPayment::whereHas('paymentStatus', fn($q) => $q->where('code', 'pending'))
            ->sum('amount');

        return view('admin.saas-payments.index', compact(
            'payments', 'subscriptions', 'statuses', 'totalCollected', 'pendingAmount'
        ));
    }

    public function create(Request $request)
    {
        $subscriptions = GymSubscription::with(['gym', 'saasPlan'])
            ->whereHas('status', fn($q) => $q->whereIn('code', ['active', 'trial']))
            ->get();

        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $paymentStatuses = PaymentStatus::orderBy('name')->get();
        $completedStatusId = PaymentStatus::where('code', 'completed')->value('id');

        // Pre-select subscription if passed via query string
        $selectedSubscriptionId = $request->query('subscription_id');
        $selectedSubscription = $selectedSubscriptionId
            ? GymSubscription::with(['gym', 'saasPlan'])->find($selectedSubscriptionId)
            : null;

        // Calculate default billing period from subscription
        $defaultPeriodStart = now()->format('Y-m-d');
        $defaultPeriodEnd = now()->addMonth()->format('Y-m-d');

        if ($selectedSubscription) {
            $defaultPeriodStart = $selectedSubscription->end_date
                ? \Carbon\Carbon::parse($selectedSubscription->end_date)->format('Y-m-d')
                : now()->format('Y-m-d');
            $defaultPeriodEnd = $selectedSubscription->billing_cycle === 'yearly'
                ? \Carbon\Carbon::parse($defaultPeriodStart)->addYear()->format('Y-m-d')
                : \Carbon\Carbon::parse($defaultPeriodStart)->addMonth()->format('Y-m-d');
        }

        return view('admin.saas-payments.create', compact(
            'subscriptions', 'paymentMethods', 'paymentStatuses',
            'completedStatusId', 'selectedSubscriptionId', 'selectedSubscription',
            'defaultPeriodStart', 'defaultPeriodEnd'
        ));
    }

    public function store(StoreSaasPaymentRequest $request)
    {
        $payment = $this->paymentService->recordPayment($request->validated());

        return redirect()
            ->route('admin.saas-payments.show', $payment)
            ->with('success', 'Payment of ' . $payment->formatted_amount . ' recorded successfully.');
    }

    public function show(SaasPayment $payment)
    {
        $payment->load([
            'subscription.gym',
            'subscription.saasPlan',
            'paymentMethod',
            'paymentStatus',
            'recordedByUser',
        ]);
        return view('admin.saas-payments.show', compact('payment'));
    }

    public function destroy(SaasPayment $payment)
    {
        $this->paymentService->deletePayment($payment);

        return redirect()
            ->route('admin.saas-payments.index')
            ->with('success', 'Payment record deleted.');
    }
}

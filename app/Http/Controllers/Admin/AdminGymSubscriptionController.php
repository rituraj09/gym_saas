<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGymSubscriptionRequest;
use App\Http\Requests\Admin\UpdateGymSubscriptionRequest;
use App\Models\Gym;
use App\Models\GymSubscription;
use App\Models\SaasPlan;
use App\Models\SaasSubscriptionStatus;
use App\Services\SaasSubscriptionService;
use Illuminate\Http\Request;

class AdminGymSubscriptionController extends Controller
{
    public function __construct(
        protected SaasSubscriptionService $subscriptionService
    ) {}

    public function index(Request $request)
    {
        $query = GymSubscription::with(['gym', 'saasPlan', 'status'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('code', $request->status));
        }

        if ($request->filled('gym_id')) {
            $query->where('gym_id', $request->gym_id);
        }

        $subscriptions = $query->paginate(15)->withQueryString();
        $gyms = Gym::orderBy('name')->get();

        return view('admin.gym-subscriptions.index', compact('subscriptions', 'gyms'));
    }

    public function create()
    {
        $gyms = Gym::orderBy('name')->get();
        $plans = SaasPlan::where('is_active', true)->orderBy('monthly_price')->get();
        $statuses = SaasSubscriptionStatus::orderBy('code')->get();
        $activeStatusId = SaasSubscriptionStatus::where('code', 'active')->value('id');

        return view('admin.gym-subscriptions.create', compact('gyms', 'plans', 'statuses', 'activeStatusId'));
    }

    public function store(StoreGymSubscriptionRequest $request)
    {
        $subscription = $this->subscriptionService->assignSubscription($request->validated());

        return redirect()
            ->route('admin.gym-subscriptions.index')
            ->with('success', 'Subscription assigned to ' . $subscription->gym->name . ' successfully.');
    }

    public function show(GymSubscription $subscription)
    {
        $subscription->load(['gym', 'saasPlan', 'status']);

        return view('admin.gym-subscriptions.show', ['subscription' => $subscription]);
    }

    public function edit(GymSubscription $subscription)
    {
        $subscription->load(['gym', 'saasPlan', 'status']);
        $plans = SaasPlan::where('is_active', true)->orderBy('monthly_price')->get();
        $statuses = SaasSubscriptionStatus::orderBy('code')->get();

        return view('admin.gym-subscriptions.edit', compact('subscription', 'plans', 'statuses'));
    }

    public function update(UpdateGymSubscriptionRequest $request, GymSubscription $subscription)
    {
        $this->subscriptionService->updateSubscription($subscription, $request->validated());

        return redirect()
            ->route('admin.gym-subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    public function cancel(GymSubscription $subscription)
    {
        $this->subscriptionService->cancelSubscription($subscription);

        return back()->with('success', 'Subscription cancelled successfully.');
    }

    public function destroy(GymSubscription $subscription)
    {
        $gymName = $subscription->gym->name;
        $subscription->delete();

        return redirect()
            ->route('admin.gym-subscriptions.index')
            ->with('success', 'Subscription for ' . $gymName . ' deleted.');
    }
}

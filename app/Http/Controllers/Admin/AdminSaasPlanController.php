<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaasPlanRequest;
use App\Http\Requests\Admin\UpdateSaasPlanRequest;
use App\Models\SaasPlan;
use App\Services\SaasSubscriptionService;

class AdminSaasPlanController extends Controller
{
    public function __construct(
        protected SaasSubscriptionService $subscriptionService
    ) {}

    public function index()
    {
        $plans = SaasPlan::withCount('gymSubscriptions')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('admin.saas-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.saas-plans.create');
    }

    public function store(StoreSaasPlanRequest $request)
    {
        $plan = $this->subscriptionService->createPlan($request->validated());

        return redirect()
            ->route('admin.saas-plans.show', $plan)
            ->with('success', 'SaaS plan created successfully.');
    }

    public function show(SaasPlan $plan)
    {
        $plan->load(['gymSubscriptions.gym', 'gymSubscriptions.status']);

        return view('admin.saas-plans.show', compact('plan'));
    }

    public function edit(SaasPlan $plan)
    {
        return view('admin.saas-plans.edit', compact('plan'));
    }

    public function update(UpdateSaasPlanRequest $request, SaasPlan $plan)
    {
        $this->subscriptionService->updatePlan($plan, $request->validated());

        return redirect()
            ->route('admin.saas-plans.show', $plan)
            ->with('success', 'SaaS plan updated successfully.');
    }

    public function toggleStatus(SaasPlan $plan)
    {
        $this->subscriptionService->togglePlanStatus($plan);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Plan {$status} successfully.");
    }

    public function destroy(SaasPlan $plan)
    {
        if ($plan->gymSubscriptions()->count() > 0) {
            return back()->with('error', 'Cannot delete a plan that has active subscriptions.');
        }

        $plan->delete();

        return redirect()
            ->route('admin.saas-plans.index')
            ->with('success', 'SaaS plan deleted successfully.');
    }
}

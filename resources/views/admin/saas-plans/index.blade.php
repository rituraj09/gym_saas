<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">SaaS Subscription Plans</h1>
            <p class="mt-1 text-sm text-gray-600">Manage subscription tiers available for gyms.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.gym-subscriptions.index') }}"
                class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 min-h-[44px]">
                <x-icon name="credit-card" class="w-4 h-4 mr-2" /> Gym Subscriptions
            </a>
            <a href="{{ route('admin.saas-plans.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> New Plan
            </a>
        </div>
    </div>

    @if ($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 {{ $plan->code === 'enterprise' ? 'border-purple-500' : ($plan->code === 'pro' ? 'border-indigo-500' : 'border-gray-400') }}">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if ($plan->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>
                        @endif

                        <div class="mt-4">
                            <span
                                class="text-3xl font-bold text-gray-900">${{ number_format($plan->monthly_price, 2) }}</span>
                            <span class="text-gray-500 text-sm">/month</span>
                            <p class="text-xs text-gray-400 mt-1">${{ number_format($plan->yearly_price, 2) }}/year</p>
                        </div>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <x-icon name="users" class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-700">{{ $plan->max_trainers_label }} Trainers</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-icon name="user" class="w-4 h-4 text-gray-400" />
                                <span class="text-gray-700">{{ $plan->max_members_label }} Members</span>
                            </div>
                            @foreach ($plan->features_list as $feature)
                                <div class="flex items-center gap-2">
                                    <x-icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-xs text-gray-400 mt-3">{{ $plan->gym_subscriptions_count }} gyms subscribed</p>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t">
                        <a href="{{ route('admin.saas-plans.show', $plan) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline min-h-[44px] flex items-center">
                            <x-icon name="eye" class="w-4 h-4 mr-1" /> Details
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.saas-plans.edit', $plan) }}"
                                class="text-gray-500 hover:text-indigo-600 min-h-[44px] flex items-center">
                                <x-icon name="pencil" class="w-4 h-4" />
                            </a>
                            <form method="POST" action="{{ route('admin.saas-plans.toggle', $plan) }}">
                                @csrf
                                <button type="submit"
                                    class="text-gray-500 hover:text-yellow-600 min-h-[44px] flex items-center">
                                    <x-icon name="arrow-path" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="credit-card" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No SaaS plans yet</h3>
            <a href="{{ route('admin.saas-plans.create') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Create Plan
            </a>
        </div>
    @endif
</x-admin-layout>

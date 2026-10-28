<x-admin-layout>
    <x-slot name="title">Subscription Details</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.gym-subscriptions.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to Subscriptions
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Subscription Details</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-2xl overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-icon name="credit-card" class="w-6 h-6 text-indigo-600" />
                <span class="font-semibold text-gray-900">{{ $subscription->gym->name }}</span>
            </div>
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800',
                    'trial' => 'bg-blue-100 text-blue-800',
                    'expired' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                    'suspended' => 'bg-yellow-100 text-yellow-800',
                ];
                $colorClass = $statusColors[$subscription->status->code] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                {{ ucfirst($subscription->status->code) }}
            </span>
        </div>

        {{-- Details --}}
        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Gym</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $subscription->gym->name }}
                        ({{ $subscription->gym->code }})</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Plan</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $subscription->plan->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Billing Cycle</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ ucfirst($subscription->billing_cycle) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Price</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        ${{ number_format($subscription->billing_cycle === 'yearly' ? $subscription->plan->yearly_price : $subscription->plan->monthly_price, 2) }}
                        / {{ $subscription->billing_cycle === 'yearly' ? 'year' : 'month' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Start Date</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        {{ $subscription->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">End Date</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'No end date' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Plan Features --}}
        <div class="px-6 py-4 bg-indigo-50 border-t border-indigo-100">
            <p class="text-xs font-semibold text-indigo-800 uppercase tracking-wide mb-2">Plan Limits</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Trainers:</span>
                    <span class="font-medium text-gray-900">{{ $subscription->plan->max_trainers ?? '∞' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Members:</span>
                    <span class="font-medium text-gray-900">{{ $subscription->plan->max_members ?? '∞' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Diet Plans:</span>
                    <span
                        class="font-medium text-gray-900">{{ $subscription->plan->has_diet_plans ? '✓' : '✗' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Reports:</span>
                    <span class="font-medium text-gray-900">{{ $subscription->plan->has_reports ? '✓' : '✗' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
            <a href="{{ route('admin.gym-subscriptions.edit', $subscription) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                <x-icon name="pencil" class="w-4 h-4" /> Edit
            </a>
            <a href="{{ route('admin.gym-subscriptions.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition min-h-[44px]">
                Back to List
            </a>
        </div>
    </div>
</x-admin-layout>

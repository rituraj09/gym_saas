<x-admin-layout>
    <x-slot name="title">{{ $gym->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.gyms.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to All Gyms
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $gym->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $gym->code }} · {{ $gym->city ?? '' }} {{ $gym->state ?? '' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Gym Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $gym->members_count }}</p>
                    <p class="text-xs text-gray-500">Members</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $gym->trainers_count }}</p>
                    <p class="text-xs text-gray-500">Trainers</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $gym->membership_plans_count }}</p>
                    <p class="text-xs text-gray-500">Plans</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-green-700">
                        ${{ number_format($gym->activeSubscription?->payments->where('paymentStatus.code', 'completed')->sum('amount') ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-gray-500">Total Paid</p>
                </div>
            </div>

            {{-- Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Gym Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Address</p>
                        <p class="text-gray-900 mt-0.5">{{ $gym->address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                        <p class="text-gray-900 mt-0.5">{{ $gym->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                        <p class="text-gray-900 mt-0.5">{{ $gym->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Registered</p>
                        <p class="text-gray-900 mt-0.5">{{ $gym->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Subscription --}}
            @php $sub = $gym->activeSubscription; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Subscription</h3>
                @if ($sub)
                    @php
                        $sCode = $sub->status?->code ?? 'unknown';
                        $sColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'trial' => 'bg-blue-100 text-blue-800',
                            'expired' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                            'suspended' => 'bg-yellow-100 text-yellow-800',
                        ];
                    @endphp
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan</span>
                            <span class="font-medium text-gray-900">{{ $sub->saasPlan?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sColors[$sCode] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($sCode) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Billing</span>
                            <span class="font-medium text-gray-900 capitalize">{{ $sub->billing_cycle }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">End Date</span>
                            <span
                                class="font-medium text-gray-900">{{ $sub->end_date?->format('M d, Y') ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.saas-payments.create', ['subscription_id' => $sub->id]) }}"
                            class="flex-1 text-center px-3 py-2 bg-green-50 text-green-700 text-xs font-medium rounded-lg min-h-[40px] flex items-center justify-center">
                            Record Payment
                        </a>
                        <a href="{{ route('admin.gym-module-access.index', $gym) }}"
                            class="flex-1 text-center px-3 py-2 bg-purple-50 text-purple-700 text-xs font-medium rounded-lg min-h-[40px] flex items-center justify-center">
                            Modules
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-400">No active subscription.</p>
                    <a href="{{ route('admin.gym-subscriptions.create') }}"
                        class="mt-3 block text-center px-3 py-2 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg min-h-[40px] flex items-center justify-center">
                        Create Subscription
                    </a>
                @endif
            </div>

            {{-- Owners --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Owner(s)</h3>
                @forelse($gym->owners as $owner)
                    <div class="flex items-center gap-3 text-sm">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-xs">
                            {{ strtoupper(substr($owner->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $owner->user?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $owner->user?->email ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No owners assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>

<x-owner-layout>
    <x-slot name="title">Membership Details</x-slot>
    <x-slot name="headerTitle">Membership Details</x-slot>

    @php
        $statusColors = [
            'active' => 'bg-green-50 text-green-700 border-green-200',
            'expiring_soon' => 'bg-amber-50 text-amber-700 border-amber-200',
            'expired' => 'bg-red-50 text-red-700 border-red-200',
            'upcoming' => 'bg-blue-50 text-blue-700 border-blue-200',
            'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
        ];
        $color = $statusColors[$membership->status->code] ?? 'bg-gray-100 text-gray-600 border-gray-200';
        $totalPaid = $membership->payments->where('status.code', 'completed')->sum('amount');
        $balance = max(0, $membership->price - $totalPaid);
    @endphp

    <div class="max-w-3xl space-y-6">
        <!-- Membership Overview -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="text-xl font-bold text-gray-900">{{ $membership->plan->name }}</h2>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                            {{ $membership->status->name }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">Member: <a
                            href="{{ route('owner.members.show', $membership->member) }}"
                            class="text-indigo-600 hover:underline font-medium">{{ $membership->member->user->name }}</a>
                    </p>
                </div>
                <a href="{{ route('owner.payments.create', ['member_id' => $membership->member_id]) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="currency" class="w-4 h-4" /> Record Payment
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Start Date</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $membership->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">End Date</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $membership->end_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Duration</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $membership->plan->duration_days }} days</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Price</p>
                    <p class="text-sm font-semibold text-indigo-600">{{ number_format($membership->price, 2) }}</p>
                </div>
            </div>

            @if ($membership->notes)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Notes</p>
                    <p class="text-sm text-gray-700">{{ $membership->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <header class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Payment Summary</h3>
                <a href="{{ route('owner.payments.create', ['member_id' => $membership->member_id]) }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add Payment</a>
            </header>
            <div class="p-5 sm:p-6 grid grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-500 mb-1">Membership Price</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($membership->price, 2) }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-green-700 mb-1">Paid</p>
                    <p class="text-lg font-bold text-green-700">{{ number_format($totalPaid, 2) }}</p>
                </div>
                <div class="bg-{{ $balance > 0 ? 'red' : 'gray' }}-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-{{ $balance > 0 ? 'red' : 'gray' }}-700 mb-1">Balance</p>
                    <p class="text-lg font-bold text-{{ $balance > 0 ? 'red' : 'gray' }}-700">
                        {{ number_format($balance, 2) }}</p>
                </div>
            </div>

            @if ($membership->payments->isNotEmpty())
                <div class="border-t border-gray-100 divide-y divide-gray-100">
                    @foreach ($membership->payments->sortByDesc('payment_date') as $payment)
                        <a href="{{ route('owner.payments.show', $payment) }}"
                            class="flex items-center justify-between px-5 sm:px-6 py-3 hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }} ·
                                    {{ $payment->method->name }}</p>
                            </div>
                            <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <a href="{{ route('owner.memberships.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">← Back to
            Memberships</a>
    </div>
</x-owner-layout>

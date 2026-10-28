<x-owner-layout>
    <x-slot name="title">Payments</x-slot>
    <x-slot name="headerTitle">Payments</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Payments</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $payments->total() }} record(s) · Total Collected: <span
                    class="font-semibold text-green-600">{{ number_format($totalAmount, 2) }}</span></p>
        </div>
        <a href="{{ route('owner.payments.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Record Payment
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by member name..."
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm">
            <button type="submit"
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">Filter</button>
            @if (request('search'))
                <a href="{{ route('owner.payments.index') }}"
                    class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition text-center min-h-[44px]">Clear</a>
            @endif
        </div>
    </form>

    @if ($payments->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <x-icon name="credit-card" class="w-8 h-8 text-gray-300 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No payments recorded</h3>
            <p class="text-sm text-gray-500 mb-5">Record your first member payment to get started.</p>
            <a href="{{ route('owner.payments.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Record
                Payment</a>
        </div>
    @else
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Member</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Amount</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Method</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Reference</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($payments as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $p->member->user->name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $p->member->member_code }}</p>
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-900">{{ number_format($p->amount, 2) }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $p->method->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $p->payment_date->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-gray-500 font-mono text-xs">
                                {{ $p->transaction_reference ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $p->status->code === 'completed' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $p->status->name }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('owner.payments.show', $p) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            @foreach ($payments as $p)
                <a href="{{ route('owner.payments.show', $p) }}"
                    class="block bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $p->member->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $p->payment_date->format('M d, Y') }} ·
                                {{ $p->method->name }}</p>
                        </div>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($p->amount, 2) }}</p>
                    </div>
                    @if ($p->transaction_reference)
                        <p class="text-xs text-gray-500 font-mono">Ref: {{ $p->transaction_reference }}</p>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    @endif
</x-owner-layout>

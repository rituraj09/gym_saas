<x-admin-layout>
    <x-slot name="title">SaaS Payments</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">SaaS Payments</h1>
            <p class="text-sm text-gray-500 mt-1">Record and track subscription payments from gyms</p>
        </div>
        <a href="{{ route('admin.saas-payments.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px] self-start">
            <x-icon name="plus-circle" class="w-4 h-4" />
            Record Payment
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-icon name="currency" class="w-5 h-5 text-green-600" />
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total Collected</p>
                    <p class="text-xl font-bold text-green-700">${{ number_format($totalCollected, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <x-icon name="exclamation" class="w-5 h-5 text-yellow-600" />
                </div>
                <div>
                    <p class="text-xs text-gray-500">Pending Amount</p>
                    <p class="text-xl font-bold text-yellow-700">${{ number_format($pendingAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.saas-payments.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Gym</label>
                <select name="gym_id" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All Gyms</option>
                    @foreach ($subscriptions as $sub)
                        <option value="{{ $sub->gym_id }}" {{ request('gym_id') == $sub->gym_id ? 'selected' : '' }}>
                            {{ $sub->gym->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->code }}" {{ request('status') === $status->code ? 'selected' : '' }}>
                            {{ ucfirst($status->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full rounded-lg border-gray-300 text-sm py-2 px-3 min-h-[44px]">
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    Filter
                </button>
                <a href="{{ route('admin.saas-payments.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($payments->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Gym</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Plan</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Method</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Amount</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Reference</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($payments as $payment)
                            @php
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'refunded' => 'bg-gray-100 text-gray-800',
                                ];
                                $colorClass =
                                    $statusColors[$payment->paymentStatus->code] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900 whitespace-nowrap">
                                    {{ $payment->payment_date->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">
                                    {{ $payment->subscription->gym->name }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $payment->subscription->saasPlan->name }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $payment->paymentMethod->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    {{ $payment->formatted_amount }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colorClass }}">
                                        {{ ucfirst($payment->paymentStatus->name) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $payment->reference_number ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.saas-payments.show', $payment) }}"
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium min-h-[44px] px-2">
                                        <x-icon name="eye" class="w-4 h-4" /> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach ($payments as $payment)
                    @php
                        $statusColors = [
                            'completed' => 'bg-green-100 text-green-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800',
                        ];
                        $colorClass = $statusColors[$payment->paymentStatus->code] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <a href="{{ route('admin.saas-payments.show', $payment) }}" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">{{ $payment->subscription->gym->name }}</span>
                            <span class="font-bold text-gray-900">{{ $payment->formatted_amount }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $payment->payment_date->format('M d, Y') }} ·
                                {{ $payment->paymentMethod->name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                {{ ucfirst($payment->paymentStatus->name) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <x-icon name="currency" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">No payments recorded yet</p>
                <p class="text-gray-400 text-sm mt-1">Click "Record Payment" to log your first SaaS payment.</p>
            </div>
        @endif
    </div>
</x-admin-layout>

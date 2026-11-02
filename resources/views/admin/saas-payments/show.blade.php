<x-admin-layout>
    <x-slot name="title">Payment Receipt</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.saas-payments.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to Payments
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Payment Details</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-2xl overflow-hidden">
        {{-- Receipt Header --}}
        <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-200 text-xs uppercase tracking-wide font-semibold">Payment Receipt</p>
                    <p class="text-3xl font-bold mt-1">{{ $payment->formatted_amount }}</p>
                </div>
                @php
                    $statusColors = [
                        'completed' => 'bg-green-400/20 text-green-100',
                        'pending' => 'bg-yellow-400/20 text-yellow-100',
                        'failed' => 'bg-red-400/20 text-red-100',
                        'refunded' => 'bg-white/20 text-white',
                    ];

                    $statusCode = $payment->paymentStatus?->code;
                    $statusName = $payment->paymentStatus?->name ?? 'Unknown';

                    $colorClass = $statusColors[$statusCode ?? ''] ?? 'bg-white/20 text-white';
                @endphp

                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                    {{ ucfirst($statusName) }}
                </span>
                <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                    {{ ucfirst($payment->paymentStatus->name) }}
                </span>
            </div>
        </div>

        {{-- Receipt Body --}}
        <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Payment ID</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        #SAP-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Payment Date</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $payment->payment_date->format('F d, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Gym</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $payment->subscription->gym->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subscription Plan</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        {{ $payment->subscription->saasPlan->name }}
                        ({{ ucfirst($payment->subscription->billing_cycle) }})
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Payment Method</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $payment->paymentMethod->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Reference</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $payment->reference_number ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Billing Period</p>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">
                        {{ $payment->billing_period_start->format('M d, Y') }}
                        —
                        {{ $payment->billing_period_end->format('M d, Y') }}
                    </p>
                </div>
            </div>

            @if ($payment->notes)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Notes</p>
                    <p class="text-sm text-gray-700">{{ $payment->notes }}</p>
                </div>
            @endif

            <div class="pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Recorded By</p>
                <p class="text-sm text-gray-700 mt-0.5">
                    {{ $payment->recordedByUser->name }}
                    on {{ $payment->created_at->format('M d, Y \a\t g:i A') }}
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center gap-3">
            <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                <x-icon name="document-text" class="w-4 h-4" /> Print Receipt
            </button>
            <form method="POST" action="{{ route('admin.saas-payments.destroy', $payment) }}"
                onsubmit="return confirm('Are you sure you want to delete this payment record?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition min-h-[44px]">
                    <x-icon name="trash" class="w-4 h-4" /> Delete
                </button>
            </form>
            <a href="{{ route('admin.saas-payments.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition min-h-[44px]">
                Back to List
            </a>
        </div>
    </div>
</x-admin-layout>

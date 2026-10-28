<x-owner-layout>
    <x-slot name="title">Payment Receipt</x-slot>
    <x-slot name="headerTitle">Payment Receipt</x-slot>

    <div class="max-w-2xl space-y-6">
        <!-- Receipt Card -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden print:border-0">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-xs uppercase tracking-wider">Payment Receipt</p>
                        <p class="text-2xl font-bold mt-1">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-white/15 backdrop-blur">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-300"></span>
                        {{ $payment->status->name }}
                    </span>
                </div>
            </div>

            <!-- Amount -->
            <div class="p-5 sm:p-6 text-center border-b border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider">Amount Paid</p>
                <p class="text-4xl sm:text-5xl font-bold text-gray-900 mt-2">{{ number_format($payment->amount, 2) }}
                </p>
                <p class="text-sm text-gray-500 mt-2">{{ $payment->payment_date->format('F d, Y') }}</p>
            </div>

            <!-- Details -->
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Member</p>
                    <a href="{{ route('owner.members.show', $payment->member) }}"
                        class="text-sm font-semibold text-indigo-600 hover:underline">{{ $payment->member->user->name }}</a>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $payment->member->member_code }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $payment->method->name }}</p>
                </div>
                @if ($payment->membership)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">For Membership</p>
                        <a href="{{ route('owner.memberships.show', $payment->membership) }}"
                            class="text-sm font-semibold text-indigo-600 hover:underline">
                            {{ $payment->membership->plan->name }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $payment->membership->start_date->format('M d, Y') }} —
                            {{ $payment->membership->end_date->format('M d, Y') }}</p>
                    </div>
                @endif
                @if ($payment->transaction_reference)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Transaction Reference</p>
                        <p class="text-sm font-mono text-gray-900">{{ $payment->transaction_reference }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 mb-1">Recorded On</p>
                    <p class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if ($payment->notes)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Notes</p>
                        <p class="text-sm text-gray-700">{{ $payment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">Thank you for your payment!</p>
                <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->gymOwner->gym->name }}</p>
            </div>
        </div>

        <!-- Receipt Attachment -->
        @if ($payment->receipt)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <header class="px-5 sm:px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Attached Receipt</h3>
                </header>
                <div class="p-5 sm:p-6">
                    @if (str_ends_with($payment->receipt, '.pdf'))
                        <a href="{{ asset('storage/' . $payment->receipt) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition text-sm font-medium">
                            <x-icon name="clipboard" class="w-4 h-4" /> View PDF Receipt
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $payment->receipt) }}" alt="Receipt"
                            class="max-w-full rounded-lg border border-gray-200">
                    @endif
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-3 print:hidden">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition min-h-[44px]">
                <x-icon name="clipboard" class="w-4 h-4" /> Print Receipt
            </button>
            <a href="{{ route('owner.payments.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 transition ml-auto self-center">←
                Back to Payments</a>
        </div>
    </div>
</x-owner-layout>

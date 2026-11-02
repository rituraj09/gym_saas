<x-admin-layout>
    <x-slot name="title">Record Payment</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.saas-payments.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" /> Back to Payments
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Record SaaS Payment</h1>
        <p class="text-sm text-gray-500 mt-1">
            Log a payment received from a gym for their subscription. If the payment is marked as "Completed", the
            subscription end date will be extended automatically.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.saas-payments.store') }}">
            @csrf

            {{-- Subscription --}}
            <div class="mb-5">
                <label for="gym_subscription_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Gym Subscription <span class="text-red-500">*</span>
                </label>
                <select name="gym_subscription_id" id="gym_subscription_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('gym_subscription_id') border-red-500 @enderror">
                    <option value="">— Select a Subscription —</option>
                    @foreach ($subscriptions as $sub)
                        <option value="{{ $sub->id }}"
                            {{ old('gym_subscription_id', $selectedSubscriptionId) == $sub->id ? 'selected' : '' }}>
                            {{ $sub->gym->name }} — {{ $sub->saasPlan->name }}
                            ({{ ucfirst($sub->billing_cycle) }})
                        </option>
                    @endforeach
                </select>
                @error('gym_subscription_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="mb-5">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Amount ($) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                    value="{{ old(
                        'amount',
                        $selectedSubscription?->billing_cycle === 'yearly'
                            ? $selectedSubscription->saasPlan->yearly_price
                            : $selectedSubscription?->saasPlan->monthly_price,
                    ) }}"
                    placeholder="0.00"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @if ($selectedSubscription)
                    <p class="text-xs text-gray-400 mt-1">
                        Suggested:
                        ${{ number_format(
                            $selectedSubscription->billing_cycle === 'yearly'
                                ? $selectedSubscription->saasPlan->yearly_price
                                : $selectedSubscription->saasPlan->monthly_price,
                            2,
                        ) }}
                        ({{ $selectedSubscription->saasPlan->name }} {{ $selectedSubscription->billing_cycle }})
                    </p>
                @endif
            </div>

            {{-- Payment Date --}}
            <div class="mb-5">
                <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Payment Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="payment_date" id="payment_date" required
                    value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('payment_date') border-red-500 @enderror">
                @error('payment_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Billing Period --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="billing_period_start" class="block text-sm font-medium text-gray-700 mb-1">
                        Period Start <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="billing_period_start" id="billing_period_start" required
                        value="{{ old('billing_period_start', $defaultPeriodStart) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('billing_period_start') border-red-500 @enderror">
                    @error('billing_period_start')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_period_end" class="block text-sm font-medium text-gray-700 mb-1">
                        Period End <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="billing_period_end" id="billing_period_end" required
                        value="{{ old('billing_period_end', $defaultPeriodEnd) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('billing_period_end') border-red-500 @enderror">
                    @error('billing_period_end')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-3 mb-5">
                The billing period this payment covers. If marked "Completed", the subscription will be extended to the
                period end date.
            </p>

            {{-- Payment Method --}}
            <div class="mb-5">
                <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Payment Method <span class="text-red-500">*</span>
                </label>
                <select name="payment_method_id" id="payment_method_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('payment_method_id') border-red-500 @enderror">
                    <option value="">— Select Method —</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}"
                            {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Status --}}
            <div class="mb-5">
                <label for="payment_status_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Payment Status <span class="text-red-500">*</span>
                </label>
                <select name="payment_status_id" id="payment_status_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('payment_status_id') border-red-500 @enderror">
                    @foreach ($paymentStatuses as $status)
                        <option value="{{ $status->id }}"
                            {{ old('payment_status_id', $completedStatusId) == $status->id ? 'selected' : '' }}>
                            {{ ucfirst($status->name) }}
                        </option>
                    @endforeach
                </select>
                @error('payment_status_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reference Number --}}
            <div class="mb-5">
                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                    Reference Number
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text" name="reference_number" id="reference_number"
                    value="{{ old('reference_number') }}" placeholder="e.g., TXN-2025-00123, Check #456"
                    maxlength="100"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('reference_number') border-red-500 @enderror">
                @error('reference_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                    placeholder="Any additional details about this payment..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    Record Payment
                </button>
                <a href="{{ route('admin.saas-payments.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>

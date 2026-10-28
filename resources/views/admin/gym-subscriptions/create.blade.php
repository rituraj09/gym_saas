<x-admin-layout>
    <x-slot name="title">New Gym Subscription</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.gym-subscriptions.index') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-2">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Back to Subscriptions
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Gym Subscription</h1>
        <p class="text-sm text-gray-500 mt-1">
            Assign a SaaS plan to a gym. The gym will gain access to features based on the selected plan.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.gym-subscriptions.store') }}">
            @csrf

            {{-- Gym --}}
            <div class="mb-5">
                <label for="gym_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Gym <span class="text-red-500">*</span>
                </label>
                <select name="gym_id" id="gym_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('gym_id') border-red-500 @enderror">
                    <option value="">— Select a Gym —</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" {{ old('gym_id') == $gym->id ? 'selected' : '' }}>
                            {{ $gym->name }} ({{ $gym->code }})
                        </option>
                    @endforeach
                </select>
                @error('gym_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Plan --}}
            <div class="mb-5">
                <label for="saas_plan_id" class="block text-sm font-medium text-gray-700 mb-1">
                    SaaS Plan <span class="text-red-500">*</span>
                </label>
                <select name="saas_plan_id" id="saas_plan_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('saas_plan_id') border-red-500 @enderror">
                    <option value="">— Select a Plan —</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('saas_plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} — ${{ number_format($plan->monthly_price, 2) }}/mo
                        </option>
                    @endforeach
                </select>
                @error('saas_plan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Billing Cycle --}}
            <div class="mb-5">
                <label for="billing_cycle" class="block text-sm font-medium text-gray-700 mb-1">
                    Billing Cycle <span class="text-red-500">*</span>
                </label>
                <select name="billing_cycle" id="billing_cycle" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('billing_cycle') border-red-500 @enderror">
                    <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>
                        Monthly
                    </option>
                    <option value="yearly" {{ old('billing_cycle') === 'yearly' ? 'selected' : '' }}>
                        Yearly (Save 17%)
                    </option>
                </select>
                @error('billing_cycle')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Date --}}
            <div class="mb-5">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="start_date" id="start_date" required
                    value="{{ old('start_date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('start_date') border-red-500 @enderror">
                @error('start_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Date (optional) --}}
            <div class="mb-5">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                    End Date
                    <span class="text-gray-400 font-normal">(leave blank for auto-calculation)</span>
                </label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('end_date') border-red-500 @enderror">
                @error('end_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">
                    If left blank, end date is auto-calculated from the billing cycle.
                </p>
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <label for="saas_subscription_status_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="saas_subscription_status_id" id="saas_subscription_status_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3 @error('saas_subscription_status_id') border-red-500 @enderror">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}"
                            {{ old('saas_subscription_status_id', $activeStatusId ?? '') == $status->id ? 'selected' : '' }}>
                            {{ ucfirst($status->code) }}
                        </option>
                    @endforeach
                </select>
                @error('saas_subscription_status_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    Create Subscription
                </button>
                <a href="{{ route('admin.gym-subscriptions.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition min-h-[44px]">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>

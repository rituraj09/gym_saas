<x-owner-layout>
    <x-slot name="title">Assign Membership</x-slot>
    <x-slot name="headerTitle">Assign Membership</x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('owner.memberships.store') }}"
            class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 space-y-5">
            @csrf
            <div>
                <label for="member_id" class="block text-sm font-medium text-gray-700 mb-1.5">Member <span
                        class="text-red-500">*</span></label>
                <select name="member_id" id="member_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 @error('member_id') border-red-500 @enderror">
                    <option value="">— Select Member —</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}"
                            {{ old('member_id', $selectedMember?->id) == $member->id ? 'selected' : '' }}>
                            {{ $member->user->name }} ({{ $member->member_code }})
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="membership_plan_id" class="block text-sm font-medium text-gray-700 mb-1.5">Membership Plan
                    <span class="text-red-500">*</span></label>
                @if ($plans->isEmpty())
                    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">No active plans
                        available. <a href="{{ route('owner.membership-plans.create') }}"
                            class="underline font-medium">Create a plan first</a>.</p>
                @else
                    <select name="membership_plan_id" id="membership_plan_id" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="">— Select Plan —</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ old('membership_plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — {{ number_format($plan->price, 2) }} ({{ $plan->duration_days }}
                                days)
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('membership_plan_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1.5">Start Date <span
                        class="text-red-500">*</span></label>
                <input type="date" name="start_date" id="start_date"
                    value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                @error('start_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">{{ old('notes') }}</textarea>
            </div>
            <div class="flex flex-col sm:flex-row-reverse gap-3 pt-2">
                <button type="submit" {{ $plans->isEmpty() ? 'disabled' : '' }}
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px] disabled:opacity-50">
                    <x-icon name="check-circle" class="w-5 h-5" /> Assign Membership
                </button>
                <a href="{{ route('owner.memberships.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition min-h-[44px]">Cancel</a>
            </div>
        </form>
    </div>
</x-owner-layout>

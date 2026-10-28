<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.member-diet-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Diet Plans
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Assign Diet Plan to Member</h1>
        <p class="mt-1 text-sm text-gray-600">Select a member and a diet plan template to assign.</p>
    </div>

    <form method="POST" action="{{ route('owner.member-diet-plans.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Assignment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member <span
                            class="text-red-500">*</span></label>
                    <select name="member_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        <option value="">Select a member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                {{ $member->user->name ?? 'No Login' }} ({{ $member->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diet Plan Template <span
                            class="text-red-500">*</span></label>
                    <select name="diet_plan_template_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        <option value="">Select a template...</option>
                        @foreach ($templates as $tpl)
                            <option value="{{ $tpl->id }}" @selected(old('diet_plan_template_id') == $tpl->id)>
                                {{ $tpl->name }}
                                @if ($tpl->target_goal)
                                    ({{ $tpl->target_goal }})
                                @endif
                                @if ($tpl->total_calories)
                                    — {{ $tpl->total_calories }} cal
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('diet_plan_template_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}"
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('start_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <p class="text-xs text-gray-400 mt-1">Leave blank for ongoing.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="e.g., No dairy, allergic to nuts"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('owner.member-diet-plans.index') }}"
                class="w-full sm:w-auto text-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">Cancel</a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4 inline mr-1" /> Assign Diet Plan
            </button>
        </div>
    </form>
</x-owner-layout>

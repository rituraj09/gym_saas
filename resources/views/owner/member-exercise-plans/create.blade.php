<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.member-exercise-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Plans
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Member Exercise Plan</h1>
        <p class="mt-1 text-sm text-gray-600">Select a member, then assign workout templates to days of the week.</p>
    </div>

    <form method="POST" action="{{ route('owner.member-exercise-plans.store') }}" x-data="planForm([])"
        class="space-y-6">
        @csrf

        {{-- Plan Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Member <span
                            class="text-red-500">*</span></label>
                    <select name="member_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                        <option value="">Select a member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                {{ $member->name }} ({{ $member->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g., John's PPL Program, 4-Week Fat Loss"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Optional description..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
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
                    <p class="text-xs text-gray-400 mt-1">Leave blank for ongoing plan.</p>
                </div>
            </div>
        </div>

        {{-- Workout Days --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Weekly Schedule <span class="text-red-500">*</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Assign a workout template to each training day.</p>
                </div>
                <button type="button" @click="addDay()"
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="plus-circle" class="w-4 h-4 mr-1" /> Add Day
                </button>
            </div>

            @error('days')
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror

            <div x-show="days.length === 0" class="text-center py-8 text-gray-400">
                <x-icon name="calendar" class="w-10 h-10 mx-auto mb-2" />
                <p class="text-sm">No workout days added. Click "Add Day" to schedule workouts.</p>
            </div>

            <div class="space-y-3">
                <template x-for="(day, index) in days" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700" x-text="'Day #' + (index + 1)"></span>
                            <button type="button" @click="removeDay(index)"
                                class="text-red-500 hover:text-red-700 min-h-[44px] min-w-[44px] flex items-center justify-center">
                                <x-icon name="x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Day of Week</label>
                                <select :name="'days[' + index + '][day_of_week]'" x-model="day.day_of_week" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                                    <option value="">Select day...</option>
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="7">Sunday</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Exercise Template</label>
                                <select :name="'days[' + index + '][exercise_template_id]'"
                                    x-model="day.exercise_template_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                                    <option value="">Select template...</option>
                                    @foreach ($templates as $tpl)
                                        <option value="{{ $tpl->id }}">
                                            {{ $tpl->name }}
                                            @if ($tpl->targetMuscleGroup)
                                                ({{ $tpl->targetMuscleGroup->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                                <input type="text" :name="'days[' + index + '][notes]'" x-model="day.notes"
                                    placeholder="e.g., Light weight, focus on form"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('owner.member-exercise-plans.index') }}"
                class="w-full sm:w-auto text-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                Cancel
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="check-circle" class="w-4 h-4 inline mr-1" />
                Create Plan
            </button>
        </div>
    </form>

    <script>
        function planForm(existingDays) {
            return {
                days: existingDays.length > 0 ? existingDays : [],
                addDay() {
                    this.days.push({
                        day_of_week: '',
                        exercise_template_id: '',
                        notes: ''
                    });
                },
                removeDay(index) {
                    this.days.splice(index, 1);
                }
            };
        }
    </script>
</x-owner-layout>

@php
    $isEditing = isset($exercise_plan) && $exercise_plan !== null;

    $existingDays = old(
        'days',
        $isEditing
            ? $exercise_plan->workoutDays
                ->map(function ($day) {
                    return [
                        'day_of_week' => $day->day_of_week,
                        'exercise_template_id' => $day->exercise_template_id,
                        'notes' => $day->notes,
                    ];
                })
                ->toArray()
            : [],
    );

    if (empty($existingDays)) {
        $existingDays = [
            [
                'day_of_week' => 1,
                'exercise_template_id' => '',
                'notes' => '',
            ],
        ];
    }

    $weekDays = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];
@endphp

<form method="POST"
    action="{{ $isEditing ? route('trainer.exercise-plans.update', Crypt::encryptString($exercise_plan->id)) : route('trainer.exercise-plans.store') }}"
    class="space-y-6">

    @csrf

    @if ($isEditing)
        @method('PATCH')
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-center gap-2 text-red-700 font-semibold text-sm">
                <x-icon name="exclamation" class="w-5 h-5" />
                Please correct the following errors.
            </div>

            <ul class="list-disc pl-8 mt-2 text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Plan details --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <x-icon name="clipboard-document" class="w-5 h-5" />
            </div>

            <div>
                <h3 class="font-bold text-gray-900">Plan Information</h3>
                <p class="text-xs text-gray-500 mt-1">Basic exercise plan details</p>
            </div>
        </div>

        <div class="p-6 space-y-5">

            {{-- Member --}}
            <div>
                <label for="member_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Select Member <span class="text-red-500">*</span>
                </label>

                <select id="member_id" name="member_id" required {{ $isEditing ? 'disabled' : '' }}
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                    <option value="">Choose a member</option>

                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id', $isEditing ? $exercise_plan->member_id : '') == $member->id)>
                            {{ $member->user?->name ?? 'Unknown Member' }}
                        </option>
                    @endforeach
                </select>

                @if ($isEditing)
                    <input type="hidden" name="member_id" value="{{ $exercise_plan->member_id }}">
                    <p class="text-xs text-gray-400 mt-1">
                        The assigned member cannot be changed when editing this plan.
                    </p>
                @endif

                @error('member_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Plan name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Plan Name <span class="text-red-500">*</span>
                </label>

                <input type="text" id="name" name="name"
                    value="{{ old('name', $isEditing ? $exercise_plan->name : '') }}" required maxlength="255"
                    placeholder="Example: Beginner Strength Training"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>

                <textarea id="description" name="description" rows="3"
                    placeholder="Describe the objective of this exercise plan..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('description', $isEditing ? $exercise_plan->description : '') }}</textarea>

                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>

                    <input type="date" id="start_date" name="start_date"
                        value="{{ old('start_date', $isEditing ? $exercise_plan->start_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                    @error('start_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        End Date
                    </label>

                    <input type="date" id="end_date" name="end_date"
                        value="{{ old('end_date', $isEditing ? $exercise_plan->end_date?->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                    @error('end_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- Workout schedule --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div
            class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <x-icon name="calendar" class="w-5 h-5" />
                </div>

                <div>
                    <h3 class="font-bold text-gray-900">Weekly Workout Schedule</h3>
                    <p class="text-xs text-gray-500 mt-1">Assign an exercise template to each workout day.</p>
                </div>
            </div>

            <button type="button" id="addWorkoutDay"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100 transition min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4" />
                Add Workout Day
            </button>
        </div>

        <div class="p-6 space-y-4" id="workoutDaysContainer">

            @foreach ($existingDays as $index => $day)
                <div class="workout-day-card border border-gray-200 rounded-xl p-5" data-index="{{ $index }}">

                    <div class="flex items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <x-icon name="calendar" class="w-4 h-4" />
                            </span>

                            <h4 class="font-semibold text-gray-900">
                                Workout Day <span class="day-number">{{ $loop->iteration }}</span>
                            </h4>
                        </div>

                        <button type="button"
                            class="remove-workout-day inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50 text-xs font-semibold transition min-h-[40px]">
                            <x-icon name="trash" class="w-4 h-4" />
                            Remove
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Day of Week <span class="text-red-500">*</span>
                            </label>

                            <select name="days[{{ $index }}][day_of_week]" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                                @foreach ($weekDays as $dayNumber => $dayName)
                                    <option value="{{ $dayNumber }}" @selected((int) ($day['day_of_week'] ?? 1) === $dayNumber)>
                                        {{ $dayName }}
                                    </option>
                                @endforeach
                            </select>

                            @error("days.$index.day_of_week")
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Exercise Template <span class="text-red-500">*</span>
                            </label>

                            <select name="days[{{ $index }}][exercise_template_id]" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                                <option value="">Select Exercise Template</option>

                                @foreach ($exerciseTemplates as $template)
                                    <option value="{{ $template->id }}" @selected(($day['exercise_template_id'] ?? '') == $template->id)>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error("days.$index.exercise_template_id")
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Workout Notes
                            </label>

                            <textarea name="days[{{ $index }}][notes]" rows="2"
                                placeholder="Optional instructions for this workout day..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $day['notes'] ?? '' }}</textarea>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

        @error('days')
            <div class="px-6 pb-5 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    {{-- Template for new workout day --}}
    <template id="workoutDayTemplate">
        <div class="workout-day-card border border-gray-200 rounded-xl p-5" data-index="__INDEX__">

            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <x-icon name="calendar" class="w-4 h-4" />
                    </span>

                    <h4 class="font-semibold text-gray-900">
                        Workout Day <span class="day-number"></span>
                    </h4>
                </div>

                <button type="button"
                    class="remove-workout-day inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50 text-xs font-semibold transition min-h-[40px]">
                    <x-icon name="trash" class="w-4 h-4" />
                    Remove
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Day of Week <span class="text-red-500">*</span>
                    </label>

                    <select name="days[__INDEX__][day_of_week]" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">
                        <option value="">Select Day</option>
                        @foreach ($weekDays as $dayNumber => $dayName)
                            <option value="{{ $dayNumber }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Exercise Template <span class="text-red-500">*</span>
                    </label>

                    <select name="days[__INDEX__][exercise_template_id]" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 min-h-[48px]">

                        <option value="">Select Exercise Template</option>

                        @foreach ($exerciseTemplates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Workout Notes
                    </label>

                    <textarea name="days[__INDEX__][notes]" rows="2" placeholder="Optional instructions for this workout day..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
                </div>

            </div>
        </div>
    </template>

    {{-- Actions --}}
    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">

        <a href="{{ route('trainer.exercise-plans.index') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition min-h-[48px]">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Cancel
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition min-h-[48px]">
            <x-icon name="check-circle" class="w-5 h-5" />
            {{ $isEditing ? 'Update Exercise Plan' : 'Create Exercise Plan' }}
        </button>

    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('workoutDaysContainer');
            const template = document.getElementById('workoutDayTemplate');
            const addButton = document.getElementById('addWorkoutDay');

            if (!container || !template || !addButton) {
                return;
            }

            let nextIndex = Math.max(
                -1,
                ...Array.from(container.querySelectorAll('.workout-day-card'))
                .map(card => Number(card.dataset.index))
            ) + 1;

            function updateDayNumbers() {
                container.querySelectorAll('.workout-day-card').forEach((card, index) => {
                    const number = card.querySelector('.day-number');

                    if (number) {
                        number.textContent = index + 1;
                    }
                });
            }

            function addWorkoutDay() {
                const html = template.innerHTML.replaceAll('__INDEX__', nextIndex);
                container.insertAdjacentHTML('beforeend', html);

                nextIndex++;
                updateDayNumbers();
            }

            addButton.addEventListener('click', addWorkoutDay);

            container.addEventListener('click', function(event) {
                const removeButton = event.target.closest('.remove-workout-day');

                if (!removeButton) {
                    return;
                }

                const cards = container.querySelectorAll('.workout-day-card');

                if (cards.length <= 1) {
                    alert('At least one workout day is required.');
                    return;
                }

                removeButton.closest('.workout-day-card').remove();
                updateDayNumbers();
            });

            updateDayNumbers();
        });
    </script>
@endpush

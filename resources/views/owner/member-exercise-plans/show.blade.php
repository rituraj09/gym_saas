<x-owner-layout>
    <x-slot name="headerTitle">Exercise Plan Details</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Navigation --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('owner.member-exercise-plans.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600 transition min-h-[44px]">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Back to Exercise Plans
            </a>

            @if (Route::has('owner.member-exercise-plans.edit'))
                <a href="{{ route('owner.member-exercise-plans.edit', Crypt::encryptString($exercise_plan->id)) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition min-h-[44px]">
                    <x-icon name="pencil" class="w-4 h-4" />
                    Edit Plan
                </a>
            @endif
        </div>

        {{-- Plan overview --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-indigo-600 text-sm font-semibold mb-2">
                            <x-icon name="fire" class="w-4 h-4" />
                            Exercise Plan
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ $exercise_plan->name }}
                        </h2>

                        <p class="text-sm text-gray-500 mt-2">
                            Assigned to
                            <span class="font-semibold text-gray-700">
                                {{ $exercise_plan->member?->user?->name ?? 'Unknown Member' }}
                            </span>
                        </p>
                    </div>

                    @if ($exercise_plan->is_active)
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Active Plan
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                            Inactive Plan
                        </span>
                    @endif
                </div>

                @if ($exercise_plan->description)
                    <div class="mt-6 p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                            Description
                        </p>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            {{ $exercise_plan->description }}
                        </p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">

                    <div class="p-4 rounded-xl bg-indigo-50/70">
                        <div class="flex items-center gap-2 text-indigo-600">
                            <x-icon name="calendar" class="w-4 h-4" />
                            <span class="text-xs font-semibold">Start Date</span>
                        </div>
                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ $exercise_plan->start_date?->format('d M Y') ?? '—' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50/70">
                        <div class="flex items-center gap-2 text-blue-600">
                            <x-icon name="calendar" class="w-4 h-4" />
                            <span class="text-xs font-semibold">End Date</span>
                        </div>
                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ $exercise_plan->end_date?->format('d M Y') ?? 'Ongoing' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-purple-50/70">
                        <div class="flex items-center gap-2 text-purple-600">
                            <x-icon name="clipboard" class="w-4 h-4" />
                            <span class="text-xs font-semibold">Workout Days</span>
                        </div>
                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ $exercise_plan->workoutDays->count() }} days
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Weekly workout schedule --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <x-icon name="calendar" class="w-5 h-5 text-indigo-600" />
                <h3 class="text-lg font-bold text-gray-900">Weekly Workout Schedule</h3>
            </div>

            @forelse ($exercise_plan->workoutDays as $day)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">

                    <div
                        class="px-5 py-4 bg-indigo-50/70 border-b border-indigo-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white text-indigo-600 flex items-center justify-center">
                                <x-icon name="fire" class="w-5 h-5" />
                            </div>

                            <div>
                                <h4 class="font-bold text-indigo-950">
                                    {{ $day->day_name }}
                                </h4>
                                <p class="text-xs text-indigo-600">
                                    Workout day
                                </p>
                            </div>
                        </div>

                        @if ($day->exerciseTemplate)
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-white text-indigo-700">
                                {{ $day->exerciseTemplate->items->count() }} exercises
                            </span>
                        @endif
                    </div>

                    @if ($day->exerciseTemplate)
                        <div class="p-5">
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">
                                    Assigned Exercise Template
                                </p>
                                <p class="text-base font-bold text-gray-900 mt-1">
                                    {{ $day->exerciseTemplate->name }}
                                </p>

                                @if ($day->exerciseTemplate->description)
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $day->exerciseTemplate->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="space-y-3">
                                @forelse ($day->exerciseTemplate->items as $item)
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-lg bg-white text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                <x-icon name="bolt" class="w-4 h-4" />
                                            </div>

                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $item->exercise?->name ?? 'Exercise unavailable' }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $item->exercise?->muscleGroup?->name ?? 'General exercise' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4 text-center sm:min-w-[240px]">
                                            <div>
                                                <p class="text-xs text-gray-400">Sets</p>
                                                <p class="font-bold text-gray-900">{{ $item->sets }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Reps</p>
                                                <p class="font-bold text-gray-900">{{ $item->reps }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Rest</p>
                                                <p class="font-bold text-gray-900">{{ $item->rest_seconds ?? 0 }}s</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-5">
                                        No exercises are available in this template.
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="p-5 text-sm text-gray-500">
                            No exercise template assigned to this day.
                        </div>
                    @endif

                    @if ($day->notes)
                        <div class="px-5 pb-5">
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-800">
                                <span class="font-semibold">Trainer's note:</span>
                                {{ $day->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                    <x-icon name="clipboard-document" class="w-10 h-10 mx-auto text-gray-300" />
                    <p class="font-semibold text-gray-800 mt-3">No workout days configured</p>
                    <p class="text-sm text-gray-500 mt-1">
                        This plan does not have a weekly schedule yet.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-owner-layout>

<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.member-exercise-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Plans
        </a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h1>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-indigo-600 font-medium mt-1">
                    <x-icon name="user" class="w-4 h-4 inline mr-1" />
                    {{ $plan->member->full_name }} ({{ $plan->member->code }})
                </p>
                @if ($plan->description)
                    <p class="text-gray-600 text-sm mt-1">{{ $plan->description }}</p>
                @endif
                <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                    <span><x-icon name="calendar"
                            class="w-3.5 h-3.5 inline mr-1" />{{ $plan->start_date->format('M d, Y') }} —
                        {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'Ongoing' }}</span>
                    <span><x-icon name="user" class="w-3.5 h-3.5 inline mr-1" />Created by
                        {{ $plan->createdBy->name }}</span>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('owner.member-exercise-plans.edit', $plan) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                </a>
                <form method="POST" action="{{ route('owner.member-exercise-plans.toggle', $plan) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                        <x-icon name="arrow-path" class="w-4 h-4 mr-1" />
                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('owner.member-exercise-plans.destroy', $plan) }}"
                    onsubmit="return confirm('Delete this exercise plan? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 min-h-[44px]">
                        <x-icon name="trash" class="w-4 h-4 mr-1" /> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Weekly Schedule --}}
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                Weekly Schedule
                <span class="text-sm font-normal text-gray-500">({{ $plan->workoutDays->count() }} training
                    days)</span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">This week: {{ \Carbon\Carbon::parse($startOfWeek)->format('M d') }} —
                {{ \Carbon\Carbon::parse($endOfWeek)->format('M d, Y') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 divide-y sm:divide-y-0 sm:divide-x">
            @for ($d = 1; $d <= 7; $d++)
                @php
                    $isToday = $d === $today;
                    $dayWorkouts = $plan->workoutDays->where('day_of_week', $d);
                    $dateStr = \Carbon\Carbon::parse($startOfWeek)
                        ->addDays($d - 1)
                        ->toDateString();
                    $dayLogs = $weeklyLogs->get($dateStr, collect());
                @endphp
                <div class="p-3 {{ $isToday ? 'bg-indigo-50 ring-2 ring-inset ring-indigo-300' : '' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold {{ $isToday ? 'text-indigo-700' : 'text-gray-500' }} uppercase">
                            {{ $dayNames[$d] }}
                        </span>
                        @if ($isToday)
                            <span
                                class="text-[10px] bg-indigo-600 text-white px-1.5 py-0.5 rounded font-medium">TODAY</span>
                        @endif
                    </div>

                    @if ($dayWorkouts->count() > 0)
                        @foreach ($dayWorkouts as $workout)
                            @php
                                $log = $dayLogs->firstWhere('member_workout_day_id', $workout->id);
                                $completed = $log && $log->isCompleted();
                                $skipped = $log && $log->isSkipped();
                            @endphp
                            <div
                                class="mb-2 p-2 rounded {{ $completed ? 'bg-green-100 border border-green-300' : ($skipped ? 'bg-gray-100 border border-gray-300' : 'bg-white border border-gray-200') }}">
                                <p class="text-xs font-semibold text-gray-900 leading-tight">
                                    @if ($completed)
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 inline text-green-600 mr-0.5" />
                                    @endif
                                    {{ $workout->exerciseTemplate->name }}
                                </p>
                                <p class="text-[10px] text-gray-500 mt-0.5">
                                    {{ $workout->exerciseTemplate->items->count() }} exercises
                                    @if ($workout->notes)
                                        &middot; {{ $workout->notes }}
                                    @endif
                                </p>

                                {{-- Log buttons --}}
                                @if (!$completed && !$skipped)
                                    <div class="flex gap-1 mt-2">
                                        <form method="POST"
                                            action="{{ route('owner.member-exercise-plans.log', $plan) }}">
                                            @csrf
                                            <input type="hidden" name="workout_day_id" value="{{ $workout->id }}">
                                            <input type="hidden" name="workout_date" value="{{ $dateStr }}">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit"
                                                class="text-[10px] bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 min-h-[32px]"
                                                title="Mark completed">
                                                <x-icon name="check-circle" class="w-3 h-3 inline mr-0.5" /> Done
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('owner.member-exercise-plans.log', $plan) }}">
                                            @csrf
                                            <input type="hidden" name="workout_day_id" value="{{ $workout->id }}">
                                            <input type="hidden" name="workout_date" value="{{ $dateStr }}">
                                            <input type="hidden" name="status" value="skipped">
                                            <button type="submit"
                                                class="text-[10px] bg-gray-400 text-white px-2 py-1 rounded hover:bg-gray-500 min-h-[32px]"
                                                title="Mark skipped">
                                                <x-icon name="x-mark" class="w-3 h-3 inline mr-0.5" /> Skip
                                            </button>
                                        </form>
                                    </div>
                                @elseif($completed)
                                    <p class="text-[10px] text-green-700 font-medium mt-1">Completed</p>
                                @elseif($skipped)
                                    <p class="text-[10px] text-gray-500 font-medium mt-1">Skipped</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-300 italic">Rest day</p>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    {{-- Template Details (expandable) --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Template Details</h2>
            <p class="text-xs text-gray-500 mt-1">Exercises in each assigned template.</p>
        </div>
        <div class="divide-y">
            @foreach ($plan->workoutDays as $workout)
                <div class="px-6 py-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded">{{ $workout->short_day }}</span>
                        <h3 class="font-semibold text-gray-900">{{ $workout->exerciseTemplate->name }}</h3>
                        @if ($workout->notes)
                            <span class="text-xs text-gray-400">— {{ $workout->notes }}</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 ml-8">
                        @foreach ($workout->exerciseTemplate->items as $item)
                            <div class="text-xs text-gray-600 flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $item->exercise->name }}</span>
                                <span class="text-gray-400">{{ $item->sets }}×{{ $item->reps }}</span>
                                @if ($item->weight_kg)
                                    <span class="text-gray-400">{{ $item->weight_kg }}kg</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-owner-layout>

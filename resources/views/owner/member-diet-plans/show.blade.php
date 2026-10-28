<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.member-diet-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Diet Plans
        </a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $plan->dietPlanTemplate->name }}</h1>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-indigo-600 font-medium mt-1">
                    <x-icon name="user" class="w-4 h-4 inline mr-1" />
                    {{ $plan->member->user->name ?? 'No Login' }} ({{ $plan->member->code }})
                </p>
                @if ($plan->notes)
                    <p class="text-gray-600 text-sm mt-1">{{ $plan->notes }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">
                    {{ $plan->start_date->format('M d, Y') }} —
                    {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'Ongoing' }}
                </p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('owner.member-diet-plans.toggle', $plan) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                        <x-icon name="arrow-path" class="w-4 h-4 mr-1" />
                        {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('owner.member-diet-plans.destroy', $plan) }}"
                    onsubmit="return confirm('Remove this diet plan?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 min-h-[44px]">
                        <x-icon name="trash" class="w-4 h-4 mr-1" /> Remove
                    </button>
                </form>
            </div>
        </div>

        {{-- Macro Summary --}}
        @php $tpl = $plan->dietPlanTemplate; @endphp
        <div class="grid grid-cols-4 gap-4 mt-4 p-4 bg-gray-50 rounded-lg">
            <div class="text-center">
                <span
                    class="block text-xl font-bold text-gray-900">{{ $tpl->total_calories ?? $tpl->calculated_calories }}</span>
                <span class="text-xs text-gray-500">Calories</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-xl font-bold text-red-600">{{ $tpl->total_protein_g ?? round($tpl->calculated_protein) }}g</span>
                <span class="text-xs text-gray-500">Protein</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-xl font-bold text-blue-600">{{ $tpl->total_carbs_g ?? round($tpl->calculated_carbs) }}g</span>
                <span class="text-xs text-gray-500">Carbs</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-xl font-bold text-yellow-600">{{ $tpl->total_fat_g ?? round($tpl->calculated_fat) }}g</span>
                <span class="text-xs text-gray-500">Fat</span>
            </div>
        </div>
    </div>

    {{-- Weekly Adherence Tracker --}}
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">This Week's Adherence</h2>
            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($startOfWeek)->format('M d') }} —
                {{ \Carbon\Carbon::parse($endOfWeek)->format('M d, Y') }}</p>
        </div>
        <div class="grid grid-cols-7 divide-x">
            @php
                $dayNames = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
            @endphp
            @for ($d = 1; $d <= 7; $d++)
                @php
                    $isToday = $d === $today;
                    $dateStr = \Carbon\Carbon::parse($startOfWeek)
                        ->addDays($d - 1)
                        ->toDateString();
                    $log = $weeklyLogs->get($dateStr);
                @endphp
                <div class="p-3 text-center {{ $isToday ? 'bg-indigo-50 ring-2 ring-inset ring-indigo-300' : '' }}">
                    <span
                        class="block text-xs font-bold {{ $isToday ? 'text-indigo-700' : 'text-gray-500' }} uppercase">{{ $dayNames[$d] }}</span>
                    @if ($log)
                        <span
                            class="inline-block mt-2 px-2 py-1 rounded text-[10px] font-medium {{ $log->status_color }}">
                            {{ $log->status_label }}
                        </span>
                    @else
                        <div class="mt-2 space-y-1">
                            <form method="POST" action="{{ route('owner.member-diet-plans.log', $plan) }}">
                                @csrf
                                <input type="hidden" name="log_date" value="{{ $dateStr }}">
                                <input type="hidden" name="status" value="followed">
                                <button type="submit"
                                    class="w-full text-[10px] bg-green-600 text-white px-1 py-1 rounded hover:bg-green-700 min-h-[28px]">
                                    <x-icon name="check-circle" class="w-3 h-3 inline" /> Done
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.member-diet-plans.log', $plan) }}">
                                @csrf
                                <input type="hidden" name="log_date" value="{{ $dateStr }}">
                                <input type="hidden" name="status" value="partially">
                                <button type="submit"
                                    class="w-full text-[10px] bg-yellow-500 text-white px-1 py-1 rounded hover:bg-yellow-600 min-h-[28px]">Partial</button>
                            </form>
                            <form method="POST" action="{{ route('owner.member-diet-plans.log', $plan) }}">
                                @csrf
                                <input type="hidden" name="log_date" value="{{ $dateStr }}">
                                <input type="hidden" name="status" value="not_followed">
                                <button type="submit"
                                    class="w-full text-[10px] bg-red-500 text-white px-1 py-1 rounded hover:bg-red-600 min-h-[28px]">Missed</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    {{-- Daily Meal Plan --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Daily Meal Plan</h2>
            <p class="text-xs text-gray-500 mt-1">Meals and food items for this diet plan.</p>
        </div>
        @foreach ($plan->dietPlanTemplate->meals as $meal)
            <div class="border-b last:border-b-0">
                <div class="px-6 py-3 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded">{{ $meal->mealType->name }}</span>
                        @if ($meal->meal_time)
                            <span
                                class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($meal->meal_time)->format('h:i A') }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $meal->total_calories }} cal</span>
                </div>
                <div class="divide-y">
                    @foreach ($meal->items as $item)
                        <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="sm:w-1/3">
                                <span class="font-medium text-gray-900 text-sm">{{ $item->food_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $item->quantity }}</span>
                            </div>
                            <div class="flex gap-4 text-xs text-gray-600 sm:ml-auto">
                                <span class="font-medium text-gray-900">{{ $item->calories }} cal</span>
                                <span class="text-red-600">P: {{ $item->protein_g }}g</span>
                                <span class="text-blue-600">C: {{ $item->carbs_g }}g</span>
                                <span class="text-yellow-600">F: {{ $item->fat_g }}g</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-owner-layout>

<x-trainer-layout>
    <x-slot name="headerTitle">{{ $diet_plan->plan_name }}</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('trainer.diet-plans.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 min-h-[44px]">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Back to Plans
        </a>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $diet_plan->plan_name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">For: {{ $diet_plan->member?->user?->name ?? '—' }}</p>
                </div>
                <span
                    class="px-3 py-1 rounded-lg text-xs font-semibold {{ $diet_plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $diet_plan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <div><span class="text-gray-500">Start</span>
                    <p class="font-medium">
                        {{ $diet_plan->start_date ? \Carbon\Carbon::parse($diet_plan->start_date)->format('M d, Y') : '—' }}
                    </p>
                </div>
                <div><span class="text-gray-500">End</span>
                    <p class="font-medium">
                        {{ $diet_plan->end_date ? \Carbon\Carbon::parse($diet_plan->end_date)->format('M d, Y') : 'Ongoing' }}
                    </p>
                </div>
                <div><span class="text-gray-500">Total Calories</span>
                    <p class="font-medium">{{ $diet_plan->total_calories ?? '—' }} kcal/day</p>
                </div>
            </div>
            @if ($diet_plan->notes)
                <div class="mt-3 p-3 bg-gray-50 rounded-xl text-sm text-gray-600">{{ $diet_plan->notes }}</div>
            @endif
        </div>

        @foreach ($diet_plan->dietPlanTemplate?->meals ?? [] as $meal)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-orange-50 border-b border-orange-100 flex items-center justify-between">
                    <h3 class="font-semibold text-orange-900">{{ $meal->mealType?->name ?? 'Meal' }}</h3>
                    @if ($meal->time)
                        <span class="text-sm text-orange-600">{{ $meal->time }}</span>
                    @endif
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($meal->items as $item)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->food_name }}</p>
                                <p class="text-sm text-gray-500">{{ $item->quantity }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-semibold text-gray-900">{{ $item->calories ?? '—' }} kcal</p>
                                <p class="text-xs text-gray-400">
                                    P: {{ $item->protein_g ?? '—' }}g ·
                                    C: {{ $item->carbs_g ?? '—' }}g ·
                                    F: {{ $item->fat_g ?? '—' }}g
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-trainer-layout>

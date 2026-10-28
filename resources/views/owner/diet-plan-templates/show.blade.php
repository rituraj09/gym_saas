<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.diet-plan-templates.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Templates
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $template->name }}</h1>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if ($template->target_goal)
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">{{ $template->target_goal }}</span>
                    @endif
                </div>
                @if ($template->description)
                    <p class="text-gray-600 mt-1">{{ $template->description }}</p>
                @endif
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('owner.diet-plan-templates.edit', $template) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                </a>
                <form method="POST" action="{{ route('owner.diet-plan-templates.destroy', $template) }}"
                    onsubmit="return confirm('Delete this diet plan template?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-600 bg-white hover:bg-red-50 min-h-[44px]">
                        <x-icon name="trash" class="w-4 h-4 mr-1" /> Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Macro Summary --}}
        <div class="grid grid-cols-4 gap-4 mt-4 p-4 bg-gray-50 rounded-lg">
            <div class="text-center">
                <span
                    class="block text-2xl font-bold text-gray-900">{{ $template->total_calories ?? $template->calculated_calories }}</span>
                <span class="text-xs text-gray-500">Calories</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-2xl font-bold text-red-600">{{ $template->total_protein_g ?? round($template->calculated_protein) }}g</span>
                <span class="text-xs text-gray-500">Protein</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-2xl font-bold text-blue-600">{{ $template->total_carbs_g ?? round($template->calculated_carbs) }}g</span>
                <span class="text-xs text-gray-500">Carbs</span>
            </div>
            <div class="text-center">
                <span
                    class="block text-2xl font-bold text-yellow-600">{{ $template->total_fat_g ?? round($template->calculated_fat) }}g</span>
                <span class="text-xs text-gray-500">Fat</span>
            </div>
        </div>
    </div>

    {{-- Meals --}}
    @foreach ($template->meals as $meal)
        <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
            <div class="px-6 py-3 bg-gray-50 border-b flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span
                        class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded">{{ $meal->mealType->name }}</span>
                    @if ($meal->meal_time)
                        <span
                            class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($meal->meal_time)->format('h:i A') }}</span>
                    @endif
                    @if ($meal->notes)
                        <span class="text-xs text-gray-400">— {{ $meal->notes }}</span>
                    @endif
                </div>
                <span class="text-xs text-gray-500">{{ $meal->total_calories }} cal &middot;
                    {{ round($meal->total_protein) }}g protein</span>
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
                        @if ($item->notes)
                            <p class="text-xs text-gray-400 italic sm:w-full">{{ $item->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</x-owner-layout>

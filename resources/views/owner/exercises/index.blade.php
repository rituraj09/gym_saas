<x-owner-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Exercise Library</h1>
        <p class="mt-1 text-sm text-gray-600">Browse all available exercises. Use filters to find what you need, then
            build templates from these exercises.</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('owner.exercises.index') }}" class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Exercise name..."
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Muscle Group</label>
                <select name="muscle_group"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Groups</option>
                    @foreach ($muscleGroups as $mg)
                        <option value="{{ $mg->id }}" @selected(request('muscle_group') == $mg->id)>{{ $mg->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Equipment</label>
                <select name="equipment"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Equipment</option>
                    @foreach ($equipmentTypes as $eq)
                        <option value="{{ $eq->id }}" @selected(request('equipment') == $eq->id)>{{ $eq->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Difficulty</label>
                <select name="difficulty"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Levels</option>
                    @foreach ($difficultyLevels as $dl)
                        <option value="{{ $dl->id }}" @selected(request('difficulty') == $dl->id)>{{ $dl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="magnifying-glass" class="w-4 h-4 inline mr-1" />
                    Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Exercise Grid --}}
    @if ($exercises->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($exercises as $exercise)
                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-gray-900 text-base">{{ $exercise->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $exercise->description }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $exercise->muscleGroup->name }}
                        </span>
                        @if ($exercise->equipmentType)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $exercise->equipmentType->name }}
                            </span>
                        @endif
                        @php
                            $diffColors = [
                                'beginner' => 'bg-green-100 text-green-800',
                                'intermediate' => 'bg-yellow-100 text-yellow-800',
                                'advanced' => 'bg-red-100 text-red-800',
                            ];
                            $colorClass = $diffColors[$exercise->difficultyLevel->code] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }}">
                            {{ $exercise->difficultyLevel->name }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $exercises->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <x-icon name="fire" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500">No exercises found matching your filters.</p>
            <a href="{{ route('owner.exercises.index') }}"
                class="text-indigo-600 text-sm hover:underline mt-2 inline-block">Clear filters</a>
        </div>
    @endif
</x-owner-layout>

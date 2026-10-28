<x-owner-layout>
    <div class="mb-6">
        <a href="{{ route('owner.exercise-templates.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Templates
        </a>
    </div>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $template->name }}</h1>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if ($template->description)
                    <p class="text-gray-600 mt-1">{{ $template->description }}</p>
                @endif
                <div class="flex flex-wrap gap-3 mt-3 text-sm text-gray-500">
                    @if ($template->targetMuscleGroup)
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                            {{ $template->targetMuscleGroup->name }}
                        </span>
                    @endif
                    @if ($template->estimated_duration_minutes)
                        <span class="inline-flex items-center">
                            <x-icon name="calendar" class="w-4 h-4 mr-1" />
                            {{ $template->estimated_duration_minutes }} min
                        </span>
                    @endif
                    <span class="inline-flex items-center">
                        <x-icon name="user" class="w-4 h-4 mr-1" />
                        Created by {{ $template->createdBy->name }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('owner.exercise-templates.edit', $template) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                </a>
                <form method="POST" action="{{ route('owner.exercise-templates.toggle', $template) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                        <x-icon name="arrow-path" class="w-4 h-4 mr-1" />
                        {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('owner.exercise-templates.destroy', $template) }}"
                    onsubmit="return confirm('Delete this template? This cannot be undone.')">
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

    {{-- Exercise List --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">
                Exercises
                <span class="text-sm font-normal text-gray-500">({{ $template->items->count() }} exercises,
                    {{ $template->items->sum('sets') }} total sets)</span>
            </h2>
        </div>

        @if ($template->items->count() > 0)
            <div class="divide-y">
                @foreach ($template->items as $index => $item)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-center gap-3 sm:w-1/3">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $item->exercise->name }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $item->exercise->muscleGroup->name }}
                                    @if ($item->exercise->equipmentType)
                                        &middot; {{ $item->exercise->equipmentType->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4 sm:gap-6 text-sm sm:ml-auto">
                            <div class="text-center">
                                <span class="block text-lg font-bold text-gray-900">{{ $item->sets }}</span>
                                <span class="text-xs text-gray-500">Sets</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-gray-900">{{ $item->reps }}</span>
                                <span class="text-xs text-gray-500">Reps</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-lg font-bold text-gray-900">{{ $item->rest_formatted }}</span>
                                <span class="text-xs text-gray-500">Rest</span>
                            </div>
                            <div class="text-center">
                                <span
                                    class="block text-lg font-bold text-gray-900">{{ $item->weight_kg ? $item->weight_kg . ' kg' : '—' }}</span>
                                <span class="text-xs text-gray-500">Weight</span>
                            </div>
                        </div>
                        @if ($item->notes)
                            <p class="text-xs text-gray-400 italic sm:w-full sm:ml-11">Note: {{ $item->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center text-gray-400">
                <x-icon name="fire" class="w-10 h-10 mx-auto mb-2" />
                <p class="text-sm">No exercises in this template yet.</p>
            </div>
        @endif
    </div>
</x-owner-layout>

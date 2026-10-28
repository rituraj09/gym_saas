<x-owner-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exercise Templates</h1>
            <p class="mt-1 text-sm text-gray-600">Create reusable workout templates to assign to members later.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('owner.member-exercise-plans.index') }}"
                class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 min-h-[44px]">
                <x-icon name="fire" class="w-4 h-4 mr-2" />
                Member Plans
            </a>
            <a href="{{ route('owner.exercises.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                <x-icon name="eye" class="w-4 h-4 mr-2" />
                Browse Library
            </a>
            <a href="{{ route('owner.exercise-templates.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" />
                New Template
            </a>
        </div>
    </div>

    @if ($templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($templates as $template)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $template->name }}</h3>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        @if ($template->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $template->description }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2 mt-3 text-xs text-gray-600">
                            @if ($template->targetMuscleGroup)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">
                                    {{ $template->targetMuscleGroup->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center">
                                <x-icon name="fire" class="w-3.5 h-3.5 mr-1" />
                                {{ $template->items->count() }} exercises
                            </span>
                            <span class="inline-flex items-center">
                                <x-icon name="bolt" class="w-3.5 h-3.5 mr-1" />
                                {{ $template->items->sum('sets') }} total sets
                            </span>
                            @if ($template->estimated_duration_minutes)
                                <span class="inline-flex items-center">
                                    <x-icon name="calendar" class="w-3.5 h-3.5 mr-1" />
                                    {{ $template->estimated_duration_minutes }} min
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t">
                        <a href="{{ route('owner.exercise-templates.show', $template) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline min-h-[44px] flex items-center">
                            <x-icon name="eye" class="w-4 h-4 mr-1" /> View
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('owner.exercise-templates.edit', $template) }}"
                                class="text-gray-600 hover:text-indigo-600 min-h-[44px] flex items-center">
                                <x-icon name="pencil" class="w-4 h-4" />
                            </a>
                            <form method="POST" action="{{ route('owner.exercise-templates.toggle', $template) }}">
                                @csrf
                                <button type="submit"
                                    class="text-gray-600 hover:text-yellow-600 min-h-[44px] flex items-center"
                                    title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}">
                                    <x-icon name="arrow-path" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="clipboard-document" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No templates yet</h3>
            <p class="text-gray-500 mt-1 text-sm">Create your first exercise template to start building workout plans
                for members.</p>
            <a href="{{ route('owner.exercise-templates.create') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" />
                Create Template
            </a>
        </div>
    @endif
</x-owner-layout>

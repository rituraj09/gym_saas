<x-owner-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Diet Plan Templates</h1>
            <p class="mt-1 text-sm text-gray-600">Create reusable diet plans with meals and macros to assign to members.
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('owner.member-diet-plans.index') }}"
                class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 min-h-[44px]">
                <x-icon name="heart" class="w-4 h-4 mr-2" /> Member Plans
            </a>
            <a href="{{ route('owner.diet-plan-templates.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> New Template
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
                        @if ($template->target_goal)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">{{ $template->target_goal }}</span>
                        @endif
                        <div class="grid grid-cols-4 gap-2 mt-3 text-center">
                            <div>
                                <span
                                    class="block text-sm font-bold text-gray-900">{{ $template->total_calories ?? $template->calculated_calories }}</span>
                                <span class="text-[10px] text-gray-500">Cal</span>
                            </div>
                            <div>
                                <span
                                    class="block text-sm font-bold text-red-600">{{ $template->total_protein_g ?? round($template->calculated_protein) }}g</span>
                                <span class="text-[10px] text-gray-500">Protein</span>
                            </div>
                            <div>
                                <span
                                    class="block text-sm font-bold text-blue-600">{{ $template->total_carbs_g ?? round($template->calculated_carbs) }}g</span>
                                <span class="text-[10px] text-gray-500">Carbs</span>
                            </div>
                            <div>
                                <span
                                    class="block text-sm font-bold text-yellow-600">{{ $template->total_fat_g ?? round($template->calculated_fat) }}g</span>
                                <span class="text-[10px] text-gray-500">Fat</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">{{ $template->meals->count() }} meals</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t">
                        <a href="{{ route('owner.diet-plan-templates.show', $template) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline min-h-[44px] flex items-center">
                            <x-icon name="eye" class="w-4 h-4 mr-1" /> View
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('owner.diet-plan-templates.edit', $template) }}"
                                class="text-gray-500 hover:text-indigo-600 min-h-[44px] flex items-center">
                                <x-icon name="pencil" class="w-4 h-4" />
                            </a>
                            <form method="POST" action="{{ route('owner.diet-plan-templates.toggle', $template) }}">
                                @csrf
                                <button type="submit"
                                    class="text-gray-500 hover:text-yellow-600 min-h-[44px] flex items-center">
                                    <x-icon name="arrow-path" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $templates->links() }}</div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="heart" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No diet plan templates yet</h3>
            <p class="text-gray-500 mt-1 text-sm">Create your first diet plan to assign meal schedules to members.</p>
            <a href="{{ route('owner.diet-plan-templates.create') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Create Template
            </a>
        </div>
    @endif
</x-owner-layout>

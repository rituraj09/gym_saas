<x-trainer-layout>
    <x-slot name="headerTitle">Diet Plans</x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <p class="text-gray-500 text-sm">All diet plans created in your gym.</p>
            <a href="{{ route('trainer.diet-plans.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition min-h-[44px]">
                <x-icon name="plus" class="w-4 h-4" />
                New Plan
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Plan Name</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Member</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">Calories
                            </th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $plan->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $plan->member?->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">
                                    {{ $plan->total_calories ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('trainer.diet-plans.show', $plan) }}"
                                            class="p-2 rounded-lg hover:bg-gray-100 min-h-[44px] min-w-[44px] flex items-center justify-center"><x-icon
                                                name="eye" class="w-4 h-4 text-gray-500" /></a>
                                        <form method="POST" action="{{ route('trainer.diet-plans.toggle', $plan) }}">
                                            @csrf @method('PATCH')
                                            <button
                                                class="p-2 rounded-lg hover:bg-gray-100 min-h-[44px] min-w-[44px] flex items-center justify-center">
                                                <x-icon name="{{ $plan->is_active ? 'pause-circle' : 'play-circle' }}"
                                                    class="w-4 h-4 {{ $plan->is_active ? 'text-orange-500' : 'text-green-500' }}" />
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('trainer.diet-plans.destroy', $plan) }}"
                                            onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="p-2 rounded-lg hover:bg-red-50 min-h-[44px] min-w-[44px] flex items-center justify-center"><x-icon
                                                    name="trash" class="w-4 h-4 text-red-500" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                    <x-icon name="cake" class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                                    <p class="font-medium">No diet plans yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($plans->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $plans->links() }}</div>
            @endif
        </div>
    </div>
</x-trainer-layout>

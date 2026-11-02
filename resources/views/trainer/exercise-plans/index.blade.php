@php
    use Illuminate\Support\Facades\Crypt;
@endphp
<x-trainer-layout>
    <x-slot name="headerTitle">Exercise Plans</x-slot>

    <div class="space-y-6">

        {{-- Page heading --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Exercise Plans</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage workout plans assigned to gym members.
                </p>
            </div>

            <a href="{{ route('trainer.exercise-plans.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition min-h-[44px]">
                <x-icon name="plus-circle" class="w-5 h-5" />
                Create Exercise Plan
            </a>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">
                <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
                <x-icon name="exclamation" class="w-5 h-5 flex-shrink-0" />
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Plans table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">All Exercise Plans</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $plans->total() }} plan(s) found
                    </p>
                </div>

                <x-icon name="clipboard-document" class="w-6 h-6 text-gray-400" />
            </div>

            @if ($plans->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="text-left px-5 py-4 font-semibold">Plan</th>
                                <th class="text-left px-5 py-4 font-semibold">Member</th>
                                <th class="text-left px-5 py-4 font-semibold hidden md:table-cell">Start Date</th>
                                <th class="text-left px-5 py-4 font-semibold hidden md:table-cell">Workout Days</th>
                                <th class="text-left px-5 py-4 font-semibold">Status</th>
                                <th class="text-right px-5 py-4 font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($plans as $plan)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $plan->name }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Plan #{{ $plan->id }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                <x-icon name="user" class="w-4 h-4" />
                                            </div>
                                            <div class="font-medium text-gray-700">
                                                {{ $plan->member?->user?->name ?? 'Unknown Member' }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 hidden md:table-cell">
                                        {{ $plan->start_date?->format('d M Y') ?? '—' }}
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 hidden md:table-cell">
                                        {{ $plan->workoutDays->count() }} days
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($plan->is_active)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-1">

                                            <a href="{{ route('trainer.exercise-plans.show', Crypt::encryptString($plan->id)) }}"
                                                title="View plan"
                                                class="w-10 h-10 inline-flex items-center justify-center rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition">
                                                <x-icon name="eye" class="w-5 h-5" />
                                            </a>

                                            @if (Route::has('trainer.exercise-plans.edit'))
                                                <a href="{{ route('trainer.exercise-plans.edit', Crypt::encryptString($plan->id)) }}"
                                                    title="Edit plan"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition">
                                                    <x-icon name="pencil" class="w-5 h-5" />
                                                </a>
                                            @endif

                                            <form method="POST"
                                                action="{{ route('trainer.exercise-plans.toggle', Crypt::encryptString($plan->id)) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    title="{{ $plan->is_active ? 'Deactivate plan' : 'Activate plan' }}"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-lg transition {{ $plan->is_active ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }}">
                                                    <x-icon name="{{ $plan->is_active ? 'x-mark' : 'check-circle' }}"
                                                        class="w-5 h-5" />
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('trainer.exercise-plans.destroy', Crypt::encryptString($plan->id)) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this exercise plan?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Delete plan"
                                                    class="w-10 h-10 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition">
                                                    <x-icon name="trash" class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $plans->links() }}
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <div
                        class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                        <x-icon name="clipboard-document" class="w-8 h-8" />
                    </div>

                    <h3 class="mt-4 font-semibold text-gray-900">No exercise plans found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Create an exercise plan to get started.
                    </p>

                    <a href="{{ route('trainer.exercise-plans.create') }}"
                        class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                        <x-icon name="plus-circle" class="w-4 h-4" />
                        Create First Plan
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-trainer-layout>

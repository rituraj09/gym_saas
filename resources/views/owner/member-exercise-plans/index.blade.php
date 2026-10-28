<x-owner-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Member Exercise Plans</h1>
            <p class="mt-1 text-sm text-gray-600">Assign weekly workout schedules to members using your exercise
                templates.</p>
        </div>
        <a href="{{ route('owner.member-exercise-plans.create') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
            <x-icon name="plus-circle" class="w-4 h-4 mr-2" />
            New Plan
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('owner.member-exercise-plans.index') }}"
        class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Member</label>
                <select name="member_id"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <option value="">All Members</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(request('member_id') == $member->id)>
                            {{ $member->name }} ({{ $member->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    Filter
                </button>
            </div>
        </div>
    </form>

    @if ($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($plans as $plan)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $plan->name }}</h3>
                                <p class="text-sm text-indigo-600 font-medium">{{ $plan->member->name }}</p>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3 text-xs text-gray-500">
                            @php
                                $uniqueDays = $plan->workoutDays->pluck('day_of_week')->unique()->sort();
                                $dayLabels = [
                                    1 => 'Mon',
                                    2 => 'Tue',
                                    3 => 'Wed',
                                    4 => 'Thu',
                                    5 => 'Fri',
                                    6 => 'Sat',
                                    7 => 'Sun',
                                ];
                            @endphp
                            @foreach ($uniqueDays as $d)
                                <span
                                    class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium">{{ $dayLabels[$d] ?? '?' }}</span>
                            @endforeach
                        </div>

                        <p class="text-xs text-gray-400 mt-2">
                            Started {{ $plan->start_date->format('M d, Y') }}
                            @if ($plan->end_date)
                                &middot; Ends {{ $plan->end_date->format('M d, Y') }}
                            @endif
                        </p>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t">
                        <a href="{{ route('owner.member-exercise-plans.show', $plan) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline min-h-[44px] flex items-center">
                            <x-icon name="eye" class="w-4 h-4 mr-1" /> View Schedule
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('owner.member-exercise-plans.edit', $plan) }}"
                                class="text-gray-500 hover:text-indigo-600 min-h-[44px] flex items-center">
                                <x-icon name="pencil" class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $plans->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="clipboard-document" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No exercise plans yet</h3>
            <p class="text-gray-500 mt-1 text-sm">Create a plan to assign weekly workouts to your members.</p>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('owner.exercise-templates.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                    <x-icon name="bolt" class="w-4 h-4 mr-2" />
                    Templates
                </a>
                <a href="{{ route('owner.member-exercise-plans.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="plus-circle" class="w-4 h-4 mr-2" />
                    New Plan
                </a>
            </div>
        </div>
    @endif
</x-owner-layout>

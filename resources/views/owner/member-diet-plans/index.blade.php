<x-owner-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Member Diet Plans</h1>
            <p class="mt-1 text-sm text-gray-600">Assign diet plan templates to members and track daily adherence.</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('owner.diet-plan-templates.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 min-h-[44px]">
                <x-icon name="heart" class="w-4 h-4 mr-2" /> Templates
            </a>
            <a href="{{ route('owner.member-diet-plans.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Assign Plan
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('owner.member-diet-plans.index') }}"
        class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Member</label>
                <select name="member_id"
                    class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[44px]">
                    <option value="">All Members</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(request('member_id') == $member->id)>
                            {{ $member->user->name ?? 'No Login' }} ({{ $member->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">Filter</button>
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
                                <h3 class="font-semibold text-gray-900">{{ $plan->dietPlanTemplate->name }}</h3>
                                <p class="text-sm text-indigo-600 font-medium">
                                    {{ $plan->member->user->name ?? 'No Login' }}</p>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if ($plan->dietPlanTemplate->target_goal)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">{{ $plan->dietPlanTemplate->target_goal }}</span>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $plan->start_date->format('M d, Y') }} —
                            {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'Ongoing' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t">
                        <a href="{{ route('owner.member-diet-plans.show', $plan) }}"
                            class="text-indigo-600 text-sm font-medium hover:underline min-h-[44px] flex items-center">
                            <x-icon name="eye" class="w-4 h-4 mr-1" /> View
                        </a>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('owner.member-diet-plans.toggle', $plan) }}">
                                @csrf
                                <button type="submit"
                                    class="text-gray-500 hover:text-yellow-600 min-h-[44px] flex items-center">
                                    <x-icon name="arrow-path" class="w-4 h-4" />
                                </button>
                            </form>
                            <form method="POST" action="{{ route('owner.member-diet-plans.destroy', $plan) }}"
                                onsubmit="return confirm('Remove this diet plan?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 hover:text-red-600 min-h-[44px] flex items-center">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $plans->links() }}</div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <x-icon name="heart" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900">No diet plans assigned yet</h3>
            <p class="text-gray-500 mt-1 text-sm">Assign a diet plan template to a member to get started.</p>
            <a href="{{ route('owner.member-diet-plans.create') }}"
                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                <x-icon name="plus-circle" class="w-4 h-4 mr-2" /> Assign Plan
            </a>
        </div>
    @endif
</x-owner-layout>

<x-owner-layout>
    <x-slot name="title">Membership Plans</x-slot>
    <x-slot name="headerTitle">Membership Plans</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Plans</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $plans->total() }} plan(s)</p>
        </div>
        <a href="{{ route('owner.membership-plans.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Plan
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search plans..."
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm">
            <select name="status"
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-sm min-w-[140px]">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit"
                class="px-4 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition min-h-[44px]">Filter</button>
        </div>
    </form>

    @if ($plans->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="clipboard" class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No membership plans yet</h3>
            <p class="text-sm text-gray-500 mb-5">Create your first plan to start assigning memberships.</p>
            <a href="{{ route('owner.membership-plans.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Create
                Plan</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($plans as $plan)
                <a href="{{ route('owner.membership-plans.show', $plan) }}"
                    class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition group">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition">
                                {{ $plan->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $plan->duration_label }}</p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($plan->price, 2) }} <span
                            class="text-sm font-normal text-gray-500">/ {{ $plan->duration_days }} days</span></p>
                    @if ($plan->description)
                        <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $plan->description }}</p>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-4">{{ $plans->links() }}</div>
    @endif
</x-owner-layout>

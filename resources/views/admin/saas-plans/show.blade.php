<x-admin-layout>
    <div class="mb-6">
        <a href="{{ route('admin.saas-plans.index') }}"
            class="text-sm text-indigo-600 hover:underline flex items-center mb-2">
            <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Back to Plans
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h1>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="text-xs text-gray-400 font-mono">{{ $plan->code }}</span>
                </div>
                @if ($plan->description)
                    <p class="text-gray-600 mt-1">{{ $plan->description }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.saas-plans.edit', $plan) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 min-h-[44px]">
                    <x-icon name="pencil" class="w-4 h-4 mr-1" /> Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="text-center">
                <span
                    class="block text-2xl font-bold text-gray-900">${{ number_format($plan->monthly_price, 2) }}</span>
                <span class="text-xs text-gray-500">Monthly</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-gray-900">${{ number_format($plan->yearly_price, 2) }}</span>
                <span class="text-xs text-gray-500">Yearly</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-gray-900">{{ $plan->max_trainers_label }}</span>
                <span class="text-xs text-gray-500">Trainers</span>
            </div>
            <div class="text-center">
                <span class="block text-2xl font-bold text-gray-900">{{ $plan->max_members_label }}</span>
                <span class="text-xs text-gray-500">Members</span>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($plan->features_list as $feature)
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <x-icon name="check-circle" class="w-3.5 h-3.5 mr-1" /> {{ $feature }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Subscribed Gyms --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Subscribed Gyms ({{ $plan->gymSubscriptions->count() }})
            </h2>
        </div>
        @if ($plan->gymSubscriptions->count() > 0)
            <div class="divide-y">
                @foreach ($plan->gymSubscriptions as $sub)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-gray-900">{{ $sub->gym->name }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $sub->gym->code }}</span>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sub->status_badge }}">
                            {{ $sub->status->name }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-400 text-sm">No gyms subscribed to this plan yet.</div>
        @endif
    </div>
</x-admin-layout>
